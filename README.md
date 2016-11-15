# Di
A most simplistic Dependency Injection Container

Services are lazy loaded by default through the use of anonymous functions.

## Basic usage

```php
// Create container
$di = new Container();

// set service
$di->set('some_service', function () {
   return new SomeService();
});

// get service
$service = $di->get('some_service');

// check if service exists
$hasService = $di->has('some_service');
```

## Dependent services

You can construct services that use other services by passing the DI container in your anonymous function.
```
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

```
$dsn = 'mysql:host=localhost;dbname=testdb';
$username = 'foo';
$password = 's3cr3t';

$di = new Container();
$di->set('database', function () use ($dsn, $username, $password) {
    return new DatabaseConnection($dsn, $username, $password);
});
```
