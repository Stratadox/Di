<?php declare(strict_types=1);

namespace Stratadox\Di;

use Closure;
use Throwable;
use Psr\Container\NotFoundExceptionInterface as NotFound;

final class DependencyContainer implements Container
{
    private $remember = [];
    private $factoryFor = [];
    private $mustReload = [];
    private $isCurrentlyResolving = [];

    public function get($theService)
    {
        $this->mustKnowAbout($theService);
        if ($this->mustReload[$theService] || $this->hasNotYetLoaded($theService)) {
            $this->remember[$theService] = $this->load($theService);
        }
        return $this->remember[$theService];
    }

    public function has($theService): bool
    {
        return isset($this->factoryFor[$theService]);
    }

    public function set(
        string $theService,
        Closure $producingTheService,
        bool $cache = true
    ): void {
        $this->remember[$theService] = null;
        $this->factoryFor[$theService] = $producingTheService;
        $this->mustReload[$theService] = !$cache;
    }

    public function forget(string $theService): void
    {
        unset(
            $this->remember[$theService],
            $this->factoryFor[$theService],
            $this->mustReload[$theService]
        );
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

    private function hasNotYetLoaded(string $theService): bool
    {
        return !isset($this->remember[$theService]);
    }

    /** @throws NotFound */
    private function mustKnowAbout(string $theService): void
    {
        if ($this->has($theService)) {
            return;
        }
        throw ServiceNotFound::noServiceNamed($theService);
    }
}
