<?php

declare(strict_types=1);

namespace Stratadox\Di;

use Psr\Container\ContainerInterface;
use ReflectionClass;

final class AutoWiring implements ContainerInterface
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
        return new self($this->container, [$interface => $class] + $this->links);
    }

    public function get($theService)
    {
        if (!$this->container->has($theService)) {
            $this->resolve($theService);
        }
        return $this->container->get($theService);
    }

    public function has($theService) : bool
    {
        return class_exists($theService) || interface_exists($theService);
    }

    private function resolve(string $service)
    {
        if (interface_exists($service)) {
            $this->resolveInterface($service);
        } else {
            $this->resolveClass($service);
        }
    }

    private function resolveInterface(string $service)
    {
        $class = $this->links[$service];
        $this->resolveClass($class);
        $this->container->set($service, function () use ($class) {
            return $this->container->get($class);
        });
    }

    private function resolveClass(string $service)
    {
        $constructor = (new ReflectionClass($service))->getConstructor();
        $dependencies = [];
        if (isset($constructor)) {
            foreach ($constructor->getParameters() as $parameter) {
                $dependency = (string) $parameter->getType();
                $this->resolve($dependency);
                $dependencies[] = $dependency;
            }
        }
        $this->container->set($service,
            function () use ($service, $dependencies) {
                $parameters = [];
                foreach ($dependencies as $dependency) {
                    $parameters[] = $this->container->get($dependency);
                }
                return new $service(...$parameters);
            }
        );
    }
}
