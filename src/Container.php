<?php

/**
 * Copyright (C) 2016 Stratadox
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Stratadox
 * @package Stratadox\Di
 */

namespace Stratadox\Di;

use ArrayAccess;
use Closure;
use Exception;
use Stratadox\Di\Exception\InvalidFactoryException;
use Stratadox\Di\Exception\InvalidServiceException;
use Stratadox\Di\Exception\UndefinedServiceException;

class Container implements ContainerInterface, ArrayAccess
{
    /** @var mixed[] */
    protected $services = [];

    /** @var Closure[] */
    protected $factories = [];

    /**
     * @param string $name
     * @param string $type = ''
     * @return mixed
     * @throws InvalidFactoryException
     * @throws InvalidServiceException
     * @throws UndefinedServiceException
     */
    public function get($name, $type = '') {
        if (!isset($this->services[$name])) {
            if (!isset($this->factories[$name])) {
                throw new UndefinedServiceException(sprintf('No service registered for %s', $name));
            }
            try {
                $this->services[$name] = $this->factories[$name]();
            } catch (Exception $e) {
                throw new InvalidFactoryException(sprintf(
                    'Service %s was configured incorrectly and could not be created: %s',
                    $name,
                    $e->getMessage()
                ), 0, $e);
            }
        }
        $service = $this->services[$name];
        if ($type !== '' && gettype($service) !== $type && !($service instanceof $type)) {
            throw new InvalidServiceException(sprintf(
                'Service %s (%s) is not of type %s',
                $name,
                is_object($service) ? get_class($service) : gettype($service),
                $type
            ));
        }
        return $service;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name) {
        return isset($this->factories[$name]);
    }

    /**
     * @param string $name
     * @param Closure $factory
     */
    public function set($name, Closure $factory) {
        $this->services[$name] = null;
        $this->factories[$name] = $factory;
    }

    /**
     * @param string $name
     */
    public function forget($name) {
        unset($this->factories[$name], $this->services[$name]);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return $this->has($offset);
    }

    /**
     * @param string $offset
     * @return mixed
     * @throws InvalidFactoryException
     * @throws InvalidServiceException
     * @throws UndefinedServiceException
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     * @param Closure $value
     */
    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset) {
        $this->forget($offset);
    }
}
