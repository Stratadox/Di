# Di [![Build Status](https://travis-ci.org/Stratadox/Di.svg?branch=master)](https://travis-ci.org/Stratadox/Di)
A most simplistic Dependency Injection Container

Services are lazy loaded through the use of anonymous functions.

## Basic usage

```php
// Create container
$di = new Container();

// Set service
$di->set('some_service', function () {
    return new SomeService();
});

// Get service
$service = $di->get('some_service');

// Check if service exists
$hasService = $di->has('some_service');

// Remove service
$di->forget('some_service');
```

Alternatively, you can use the array syntax:

```php
// Create container
$di = new ArrayAdapter(new Container());

// Set service
$di['some_service'] = function () {
    return new SomeService();
};

// Get service
$service = $di['some_service'];

// Check if service exists
$hasService = isset($di['some_service']);

// Remove service
unset($di['some_service']);
```

## Dependent services

You can construct services that use other services by passing the DI container in your anonymous function.

```php
$di = new Container();

$di->set('collaborator', function () {
    return new Collaborator();
});

$di->set('main_service', function () use ($di) {
    return new MainService($di->get('collaborator');
});

$service = $di->get('main_service');
```

Because services are lazy it does not matter in which order you define them, as long they are all defined when you request one.
So in the example above we could define `main_service` before `collaborator`, so long as we don't request `main_service` before `collaborator` is defined.

## Parameters

To pass other parameters to your services, pass them to your anonymous function as well.

```php
$dsn = 'mysql:host=localhost;dbname=testdb';
$username = 'admin';
$password = 'secret';

$di = new Container();
$di->set('database', function () use ($dsn, $username, $password) {
    return new DatabaseConnection($dsn, $username, $password);
});
```

## Typehinting

You can assert the service to be of a certain class or implement an interface when requesting the service.
```php
$foo = $di->get('foo', Foo::class);
```
## Cache

By default, services are cached. You can trigger the factory on each request by setting cache to false.
```php
// Set service
$di->set('some_service', function () {
    return new SomeService();
}, false);
```
