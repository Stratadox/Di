<?php

namespace Stratadox\Di\Test;

use Stratadox\Di\Container;
use Stratadox\Di\Exception\InvalidFactoryException;
use Stratadox\Di\Exception\InvalidServiceException;
use Stratadox\Di\Exception\UndefinedServiceException;
use Stratadox\Di\Test\Stub\Bar;
use Stratadox\Di\Test\Stub\BarInterface;
use Stratadox\Di\Test\Stub\Baz;
use Stratadox\Di\Test\Stub\Foo;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldIndicateWhenServiceIdIsSet()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertTrue($di->has('foo'));
    }

    /**
     * @test
     */
    public function shouldIndicateUnsetServiceIds()
    {
        $di = new Container();
        $this->assertFalse($di->has('foo'));
    }

    /**
     * @test
     */
    public function shouldReturnExistingService()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });

        $foo = $di->get('foo');

        $this->assertInstanceOf(Foo::class, $foo);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectService()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });
        $di->set('bar', function () {
            return new Bar();
        });

        $this->assertInstanceOf(Foo::class, $di->get('foo'));
        $this->assertInstanceOf(Bar::class, $di->get('bar'));
    }

    /**
     * @test
     */
    public function shouldAllowServiceOverride()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Bar();
        });
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertInstanceOf(Foo::class, $di->get('foo'));
    }

    /**
     * @test
     */
    public function shouldAllowServiceComposition()
    {
        $di = new Container();
        $di->set('baz', function () use ($di) {
            return new Baz($di->get('foo'));
        });
        $di->set('foo', function () {
            return new Foo();
        });

        $this->assertInstanceOf(Foo::class, $di->get('baz')->getFoo());
    }

    /**
     * @test
     */
    public function shouldCacheCompositeServices()
    {
        $di = new Container();
        $di->set('baz', function () use ($di) {
            return new Baz($di->get('foo'));
        });
        $di->set('foo', function () {
            return new Foo();
        });

        $baz1 = $di->get('baz');

        // Override collaborator 'foo' with new service
        $di->set('foo', function () {
            return new Bar();
        });

        // The override should not have any effect on the 'baz' service
        $baz2 = $di->get('baz');

        $this->assertSame($baz1, $baz2);
    }

    /**
     * @test
     */
    public function shouldIndicateInvalidFactories()
    {
        $di = new Container();
        $di->set('baz', function () use ($di) {
            return new Baz($di->get('foo', Foo::class));
        });
        $di->set('foo', function () {
            return new Bar();
        });

        // Invalid because service 'foo' has a Bar, not a Foo
        $this->expectException(InvalidFactoryException::class);
        $di->get('baz');
    }

    /**
     * @test
     */
    public function shouldNotReturnNonExistingService()
    {
        $di = new Container();

        $this->expectException(UndefinedServiceException::class);
        $di->get('foo');
    }

    /**
     * @test
     */
    public function shouldReturnCachedInstances()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $bar1 = $di->get('bar');
        $bar2 = $di->get('bar');

        $this->assertSame($bar1, $bar2);
    }

    /**
     * @test
     */
    public function shouldReturnServiceOnCorrectInterface()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $bar = $di->get('bar', BarInterface::class);

        $this->assertInstanceOf(BarInterface::class, $bar);
    }

    /**
     * @test
     */
    public function shouldNotReturnServiceOnInterfaceMismatch()
    {
        $di = new Container();
        $di->set('bar', function () {
            return new Bar();
        });

        $this->expectException(InvalidServiceException::class);
        $di->get('bar', Foo::class);
    }

    /**
     * @test
     */
    public function shouldReturnScalarOnCorrectType()
    {
        $di = new Container();
        $di->set('string', function () {
            return 'Hello world!';
        });

        $this->assertEquals('Hello world!', $di->get('string', 'string'));
    }

    /**
     * @test
     */
    public function shouldNotReturnScalarOnTypeMismatch()
    {
        $di = new Container();
        $di->set('string', function () {
            return 'Hello world!';
        });

        $this->expectException(InvalidServiceException::class);
        $di->get('string', 'double');
    }

    /**
     * @test
     */
    public function shouldNotHaveServiceAfterUnset()
    {
        $di = new Container();
        $di->set('foo', function () {
            return new Foo();
        });
        $this->assertTrue($di->has('foo'));

        $di->forget('foo');
        $this->assertFalse($di->has('foo'));
	}
}
