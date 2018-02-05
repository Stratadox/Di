<?php

declare(strict_types=1);

namespace Stratadox\Di;

use ReflectionClass;

final class AutoWiring
{
    private $container;

    private function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function the(ContainerInterface $container) : self
    {
        return new self($container, []);
    }

    public function get(string $service)
    {
        if (!$this->container->has($service)) {
            $this->resolve($service);
        }
        return $this->container->get($service);
    }

    private function resolve(string $service)
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
