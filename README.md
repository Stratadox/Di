# Di 

[![Build Status](https://travis-ci.org/Stratadox/Di.svg?branch=master)](https://travis-ci.org/Stratadox/Di)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/Di/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/Di?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/Di/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/Di/?branch=master)

A minimalistic Dependency Injection Container

Services are lazy loaded through the use of anonymous functions.

## Installation

Install using composer:

```
composer require stratadox/di
```

## Basic usage

```php
// Create container
$container = new DependencyContainer();

// Set service
$container->set('some_service', function () {
    return new SomeService();
});

// Get service
$service = $container->get('some_service');

// Check if service exists
$hasService = $container->has('some_service');

// Remove service
$container->forget('some_service');
```

Alternatively, you can use the array syntax:

```php
// Create container
$container = new ArrayAdapter(new DependencyContainer());

// Set service
$container['some_service'] = function () {
    return new SomeService();
};

// Get service
$service = $container['some_service'];

// Check if service exists
$hasService = isset($container['some_service']);

// Remove service
unset($container['some_service']);
```

By decorating the container with an AutoWiring object, a large portion of the 
configuration effort can be automated:

```php
// Create container
$container = AutoWiring::the(new DependencyContainer);

$foo = $container->get(Foo::class);
```


## Dependent services

You can construct services that use other services by passing the DI container in your anonymous function.

```php
$container = new DependencyContainer();

$container->set('collaborator', function () {
    return new Collaborator();
});

$container->set('main_service', function () use ($container) {
    return new MainService($container->get('collaborator'));
});

$service = $container->get('main_service');
```

Because services are lazy it does not matter in which order you define them, as long they are all defined when you request one.
So in the example above we could define `main_service` before `collaborator`, so long as we don't request `main_service` before `collaborator` is defined.

## Parameters

To pass other parameters to your services, pass them to your anonymous function as well.

```php
$dsn = 'mysql:host=localhost;dbname=testdb';
$username = 'admin';
$password = 'secret';

$container = new DependencyContainer();
$container->set('database', function () use ($dsn, $username, $password) {
    return new DatabaseConnection($dsn, $username, $password);
});
```

## Cache

By default, services are cached. You can trigger the factory on each request by setting cache to false.
```php
// Set service
$container->set('some_service', function () {
    return new SomeService();
}, false);
```
