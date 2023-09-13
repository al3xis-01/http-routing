# PHP Routing Library: php-routing

A lightweight and flexible routing library for PHP applications. Simplify the management of routes, define endpoints, and handle URL routing seamlessly within your web projects using this easy-to-use PHP routing library.

![Badge Developing](https://img.shields.io/badge/STATUS-DEVELOPING-green)


## Installation

This library is currently not available on Composer, so it can only be used locally. You can clone the repository and use it as follows:

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = new \Yolanda\Routing\Router();

$router->name('route1')
    ->get('/route1/{digit}', [\Routing\Test::class, 'funcion1'])
    ->done();

$router->name('route2')
    ->post('/route2/:digit2', [\Routing\Test::class, 'funcion2'])
    ->done();

$resultOfCurrentRoute    =    $router->done();

// Print a result of current route
echo    $resultOfCurrentRoute;
```

## Project Structure

``` bash
.
├── LICENSE
├── README.md
├── composer.json
├── phpunit.xml
├── src
│   ├── Exceptions
│   │   ├── InvalidFunctionException.php
│   │   ├── InvalidFunctionParametersException.php
│   │   ├── InvalidNameForRouteException.php
│   │   ├── InvalidUrlParametersException.php
│   │   └── RouteNotFoundException.php
│   ├── Route.php
│   ├── RouteCollection.php
│   ├── RouteRegister.php
│   ├── Router.php
│   └── Trait
│       └── RouteFunctions.php
└── tests
    ├── RouteTest.php
    └── RouterTest.php

5 directories, 16 files
```
## Credits

<a href="https://github.com/al3xis-01">
  <img src="https://avatars.githubusercontent.com/u/81994472?v=4" width="100"> 
</a>
