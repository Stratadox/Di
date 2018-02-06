<?php

declare(strict_types=1);

namespace Stratadox\Di;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Throwable;

final class Container implements ContainerInterface, PsrContainerInterface
{
    protected $remember = [];
    protected $factoryFor = [];
    protected $mustReload = [];
    protected $isCurrentlyResolving = [];

    public function get($theService, string $mustHaveThisType = '')
    {
        $this->mustKnowAbout($theService);

        if ($this->hasNotYetLoaded($theService) or $this->mustReload[$theService]) {
            $this->remember[$theService] = $this->load($theService);
        }

        $ourService = $this->remember[$theService];
        $this->typeMustCheckOut($theService, $ourService, $mustHaveThisType);
        return $ourService;
    }

    public function has($theService) : bool
    {
        return isset($this->factoryFor[$theService]);
    }

    public function set(
        string $theService,
        Closure $producingTheService,
        bool $cache = true
    ) : void
    {
        $this->remember[$theService] = null;
        $this->factoryFor[$theService] = $producingTheService;
        $this->mustReload[$theService] = !$cache;
    }

    public function forget(string $theService) : void
    {
        unset($this->remember[$theService]);
        unset($this->factoryFor[$theService]);
        unset($this->mustReload[$theService]);
    }

    /** @throws InvalidServiceDefinition */
    private function load(string $theService)
    {
        if (isset($this->isCurrentlyResolving[$theService])) {
            throw DependenciesCannotBeCircular::loopDetectedIn($theService);
        }
        $this->isCurrentlyResolving[$theService] = true;

        $makeTheService = $this->factoryFor[$theService];
        try {
            return $makeTheService();
        } catch (Throwable $encounteredException) {
            throw InvalidFactory::threwException($theService, $encounteredException);
        } finally {
            unset($this->isCurrentlyResolving[$theService]);
        }
    }

    private function hasNotYetLoaded(string $theService) : bool
    {
        return !isset($this->remember[$theService]);
    }

    /** @throws ServiceNotFound */
    private function mustKnowAbout(string $theService) : void
    {
        if ($this->has($theService)) return;
        throw ServiceNotFound::noServiceNamed($theService);
    }

    /** @throws InvalidServiceDefinition */
    private function typeMustCheckOut(string $serviceName, $service, string $requiredType) : void
    {
        if (empty($requiredType)) return;
        if (gettype($service) === $requiredType) return;
        if ($service instanceof $requiredType) return;
        throw InvalidServiceType::serviceIsNotOfType($serviceName, $requiredType);
    }
}
