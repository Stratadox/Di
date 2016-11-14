<?php

namespace Stratadox\Di\Test;

use Stratadox\Di\Container;
use Stratadox\Di\Exception\InvalidServiceException;
use Stratadox\Di\Exception\UndefinedServiceException;
use Stratadox\Di\Test\Asset\Bar;
use Stratadox\Di\Test\Asset\BarInterface;
use Stratadox\Di\Test\Asset\Baz;
use Stratadox\Di\Test\Asset\Foo;
use Stratadox\Di\Test\Asset\Qux;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Asserts that the Foo service is configured and loaded correctly
     */
    public function testSetAndGetService()
    {
        $di = new Container();

        $di->set('foo', function() {
            return new Foo();
        });

        $this->assertTrue($di->has('foo'));

        $foo = $di->get('foo', Foo::class);

        $this->assertEquals('something', $foo->doSomething());
    }

    /**
     * Asserts that an UndefinedServiceException is thrown when the service does not implement the required service
     */
    public function testGetUndefinedServiceThrowsException()
    {
        $di = new Container();

        $this->assertFalse($di->has('foo'));

        $this->expectException(UndefinedServiceException::class);

        $di->get('foo');
    }

    /**
     * Asserts that the Bar service is configured, loaded and interface-compliant
     */
    public function testSetGetAndValidateService()
    {
        $di = new Container();

        $di->set('bar', function() {
            return new Bar();
        });

        $this->assertTrue($di->has('bar'));

        $bar = $di->get('bar', BarInterface::class);

        $this->assertEquals('something-useful', $bar->doSomethingUseful());
    }

    /**
     * Asserts that an InvalidServiceException is thrown when the service does not implement the required interface
     */
    public function testSetAndGetInvalidServiceThrowsException()
    {
        $di = new Container();

        $di->set('foo', function() {
            return new Foo();
        });

        $this->assertTrue($di->has('foo'));

        $this->expectException(InvalidServiceException::class);

        $di->get('foo', BarInterface::class);
    }

    /**
     * Asserts that the Bar service is replaced by the Baz service at runtime
     */
    public function testOverwriteService()
    {
        $di = new Container();

        $di->set('barbaz', function() {
            return new Bar();
        });

        $this->assertTrue($di->has('barbaz'));

        $bar = $di->get('barbaz', BarInterface::class);

        $this->assertEquals('something-useful', $bar->doSomethingUseful());

        $di->set('barbaz', function() {
            return new Baz();
        });

        $bar = $di->get('barbaz', BarInterface::class);

        $this->assertEquals('something-equally-useful', $bar->doSomethingUseful());
    }

    /**
     * Asserts that services can be loaded in though an array of service configurations
     */
    public function testSetServicesAsArray()
    {
        $di = new Container();

        $services = [
            'foo' => function() { return new Foo(); },
            'bar' => function() { return new Bar(); },
            'baz' => function() { return new Baz(); },
        ];

        $di->configure($services);

        $this->assertTrue($di->has('foo'));
        $this->assertTrue($di->has('bar'));
        $this->assertTrue($di->has('baz'));
    }

    /**
     * Asserts that services can be loaded in though an array of service configurations
     */
    public function testPartiallyOverwriteServicesThroughArray()
    {
        $di = new Container();

        $services = [
            'foo' => function() { return new Foo(); },
            'bar' => function() { return new Bar(); },
            'qux' => function() { return new Qux(new Container()); },
        ];

        $di->configure($services);

        $this->assertTrue($di->has('foo'));
        $this->assertTrue($di->has('bar'));
        $this->assertTrue($di->has('qux'));

        $services = [
            'baz' => function() { return new Baz(); },
            'qux' => function() use ($di) { return new Qux($di); },
        ];
        $di->configure($services);

        $this->assertTrue($di->has('foo'));
        $this->assertTrue($di->has('bar'));
        $this->assertTrue($di->has('baz'));
        $this->assertTrue($di->has('qux'));
    }

    /**
     * Asserts that the container can be passed as constructor argument to a service
     */
    public function testInjectContainerIntoService()
    {
        $di = new Container();

        $services = [
            'barbaz' => function() { return new Bar(); },
            'qux' => function() use ($di) { return new Qux($di); },
        ];

        $di->configure($services);

        $this->assertTrue($di->has('barbaz'));
        $this->assertTrue($di->has('qux'));

        $qux = $di->get('qux', Qux::class);

        $this->assertEquals('something-useful', $qux->doSomethingUseful());
    }

    /**
     * Asserts that a service changes behaviour if a dependency gets modified
     */
    public function testInjectedContainerCanChangeService()
    {
        $di = new Container();

        $services = [
            'barbaz' => function() { return new Bar(); },
            'qux' => function() use ($di) { return new Qux($di); },
        ];
        
        $di->configure($services);

        $this->assertTrue($di->has('barbaz'));
        $this->assertTrue($di->has('qux'));

        $qux = $di->get('qux', Qux::class);

        $this->assertEquals('something-useful', $qux->doSomethingUseful());

        $di->set('barbaz', function() {
            return new Baz();
        });

        $this->assertEquals('something-equally-useful', $qux->doSomethingUseful());
    }

    /**
     * Asserts that the service is not loaded before calling $di->get
     */
    public function testServiceIsLazyLoaded()
    {
        $di = new Container();

        $di->set('throw', function () {
            throw new \Exception();
        });

        $this->assertTrue($di->has('throw'));

        $this->expectException(\Exception::class);

        $di->get('throw');
    }

    /**
     * Asserts that the interface argument is optional
     */
    public function testInterfaceIsOptional()
    {
        $di = new Container();

        $di->set('foo', function() {
            return new Foo();
        });

        $this->assertTrue($di->has('foo'));

        $foo = $di->get('foo');

        $this->assertEquals('something', $foo->doSomething());
    }
}