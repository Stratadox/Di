<?php

declare(strict_types=1);

namespace Stratadox\Di;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use ReflectionClass as Reflected;
use ReflectionException;
use ReflectionType;

final class AutoWiring implements ContainerInterface, PsrContainerInterface
{
    private $container;
    private $links;

    private function __construct(ContainerInterface $container, array $links)
    {
        $this->container = $container;
        $this->links = $links;
    }

    public static function the(ContainerInterface $container) : self
    {
        return new self($container, []);
    }

    public function link(string $interface, string $class) : self
    {
        if (!is_a($class, $interface, true)) {
            throw InvalidServiceType::serviceIsNotOfType($class, $interface);
        }
        return new self($this->container, [$interface => $class] + $this->links);
    }

    public function get($theService, string $type = '')
    {
        if (!$this->container->has($theService)) {
            $this->resolve($theService);
        }
        return $this->container->get($theService);
    }

    public function has($theService) : bool
    {
        return class_exists($theService)
            || isset($this->links[$theService])
            || $this->container->has($theService);
    }

    public function set(string $service, Closure $factory, bool $cache = true)
    {
        $this->container->set($service, $factory, $cache);
    }

    public function forget(string $service)
    {
        $this->container->forget($service);
    }

    private function resolve(string $service)
    {
        try {
            $this->resolveThe(new Reflected($service));
        } catch (ReflectionException $exception) {
            throw ServiceNotFound::noServiceNamed($service);
        }
    }

    private function resolveThe(Reflected $service)
    {
        if ($service->isAbstract() || $service->isInterface()) {
            $this->resolveAbstract($service);
        } else {
            $this->resolveClass($service);
        }
    }

    private function resolveAbstract(Reflected $service) : void
    {
        $name = $service->getName();
        if (!isset($this->links[$name])) {
            throw CannotResolveAbstractType::noLinkDefinedFor($service);
        }
        $class = $this->links[$name];
        $this->resolveClass(new Reflected($class));
        $this->container->set($name, function () use ($class) {
            return $this->container->get($class);
        });
    }

    private function resolveClass(Reflected $service) : void
    {
        $name = $service->getName();
        $constructor = $service->getConstructor();
        $dependencies = [];
        if (isset($constructor)) {
            foreach ($constructor->getParameters() as $parameter) {
                $dependencies[] = $this->handleDependency($parameter->getType());
            }
        }
        $container = $this->container;
        $container->set($name, function () use ($name, $dependencies, $container) {
            $parameters = [];
            foreach ($dependencies as $dependency) {
                $parameters[] = $container->get($dependency);
            }
            return new $name(...$parameters);
        });
    }

    private function handleDependency(ReflectionType $theType) : string
    {
        if ($theType->isBuiltin()) {
            throw CannotAutoWireBuiltInTypes::cannotResolve($theType);
        }
        $theDependency = (string) $theType;
        if (!$this->container->has($theDependency)) {
            $this->resolve($theDependency);
        }
        return $theDependency;
    }
}
