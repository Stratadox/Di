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

namespace Stratadox\Di\Test;

use PHPUnit\Framework\TestCase;
use Stratadox\Di\ArrayAdapter;
use Stratadox\Di\Container;
use Stratadox\Di\Test\Stub\Foo;

class ArrayAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeAbleToGetService()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        $this->assertSame($container->get('foo'), $di['foo']);
    }

    /**
     * @test
     */
    public function shouldBeAbleToSetService()
    {
        $container = new Container();

        $di = new ArrayAdapter($container);
        $di['foo'] = function () {
            return new Foo();
        };

        $this->assertTrue($container->has('foo'));

        $this->assertSame($container->get('foo'), $di['foo']);
    }

    /**
     * @test
     */
    public function shouldBeAbleToUnsetServices()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        unset($di['foo']);

        $this->assertFalse($container->has('foo'));
    }

    /**
     * @test
     */
    public function shouldIndicateThatServiceExists()
    {
        $container = new Container();
        $container->set('foo', function () {
            return new Foo();
        });

        $di = new ArrayAdapter($container);

        $this->assertTrue(isset($di['foo']));
        $this->assertArrayHasKey('foo', $di);
    }

    /**
     * @test
     */
    public function shouldIndicateThatServiceDoesNotExist()
    {
        $di = new ArrayAdapter(new Container());

        $this->assertFalse(isset($di['foo']));
        $this->assertArrayNotHasKey('foo', $di);
    }
}
