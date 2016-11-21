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

class ArrayAdapter implements ArrayAccess
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return $this->container->has($offset);
    }

    /**
     * @param string $offset
     * @return mixed
     * @throws InvalidFactoryException
     * @throws InvalidServiceException
     * @throws UndefinedServiceException
     */
    public function offsetGet($offset) {
        return $this->container->get($offset);
    }

    /**
     * @param string $offset
     * @param Closure $value
     */
    public function offsetSet($offset, $value) {
        $this->container->set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset) {
        $this->container->forget($offset);
    }
}
