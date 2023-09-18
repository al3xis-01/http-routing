# HTTP Routing Library: http-routing

A lightweight and flexible routing library for PHP applications. Simplify the management of routes, define endpoints, and handle URL routing seamlessly within your web projects using this easy-to-use PHP routing library.

![Badge Developing](https://img.shields.io/badge/STATUS-DEVELOPING-green)


## Installation

This library is currently not available on Composer, so it can only be used locally. You can clone the repository and use it as follows:

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Test{
    public function __construct()
    {
    }

    public function funcion1(int $digit): string
    {

        return number_format($digit * pi(), 2);
    }

}

function funcion2(string $digit2): string
{
    return 'ok: '. $digit2;
}

$router     =   new \Yolanda\Http\Routing\Router();

$test   =   new Test();

$router->name('route1')
    ->get('/route1/{digit}', [Test::class,'funcion1'])
    ->done();

$router->name('route2')
    ->get('/route2/:digit2', 'funcion2')
    ->done();

$router->name('route3')
    ->get('/route3/:digit', [$test, 'funcion1'])
    ->done();

$uri    =   $_SERVER['REQUEST_URI'];
$method =   $_SERVER['REQUEST_METHOD'];

print $router->done($uri, $method);

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
