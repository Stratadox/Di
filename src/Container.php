<?php

namespace Stratadox\Di;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Stratadox\Di\Exception\DependenciesCannotBeCircular;
use Stratadox\Di\Exception\InvalidFactory;
use Stratadox\Di\Exception\InvalidServiceDefinition;
use Stratadox\Di\Exception\InvalidServiceType;
use Stratadox\Di\Exception\ServiceNotFound;
use Throwable;

class Container implements ContainerInterface, PsrContainerInterface
{
    protected $remember = [];
    protected $factoryFor = [];
    protected $mustReload = [];
    protected $isCurrentlyResolving = [];

    /** @inheritdoc */
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

    /** @inheritdoc */
    public function has($theService) : bool
    {
        return isset($this->factoryFor[$theService]);
    }

    /** @inheritdoc */
    public function set(
        string $theService,
        Closure $producingTheService,
        bool $cache = true
    ) {
        $this->remember[$theService] = null;
        $this->factoryFor[$theService] = $producingTheService;
        $this->mustReload[$theService] = !$cache;
    }

    /** @inheritdoc */
    public function forget(string $theService)
    {
        unset($this->remember[$theService]);
        unset($this->factoryFor[$theService]);
        unset($this->mustReload[$theService]);
    }

    /** @throws InvalidServiceDefinition */
    protected function load(string $theService)
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

    private function hasNotYetLoaded(string $theService)
    {
        return !isset($this->remember[$theService]);
    }

    /** @throws ServiceNotFound */
    private function mustKnowAbout(string $theService)
    {
        if ($this->has($theService)) return;
        throw ServiceNotFound::noServiceNamed($theService);
    }

    /** @throws InvalidServiceDefinition */
    private function typeMustCheckOut(string $serviceName, $service, string $requiredType)
    {
        if (empty($requiredType)) return;
        if (gettype($service) === $requiredType) return;
        if ($service instanceof $requiredType) return;
        throw InvalidServiceType::serviceIsNotOfType($serviceName, $requiredType);
    }
}
