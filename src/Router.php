<?php

namespace Yolanda\Http\Routing;

use ReflectionException;
use Yolanda\Http\Routing\Exceptions\InvalidFunctionParametersException;
use Yolanda\Http\Routing\Exceptions\InvalidUrlParametersException;
use Yolanda\Http\Routing\Exceptions\RouteNotFoundException;
use Yolanda\Http\Routing\Trait\RouteFunctions;

/**
 * Route management class
 *
 */
class Router
{

    use RouteFunctions;

    /**
     * @var RouteCollection
     */
    private RouteCollection $collection;
    /**
     * @var RouteRegister
     */
    private RouteRegister   $register;

    /**
     *
     */
    public function __construct()
    {
        $this->collection   =   new RouteCollection();
        $this->register     =   new RouteRegister($this->collection);
    }


    /**
     * @param string $value
     * @return RouteRegister
     */
    public function name(string $value): RouteRegister
    {
        return $this->register->name($value);
    }

    /**
     * Performs route comparison based on uri and http method
     *
     * @param string $uri
     * @param string $method
     * @return bool|Route
     */
    public function match(string $uri, string $method = 'GET'): bool|Route
    {

        $routes =   array_filter($this->collection->all(), fn($i)=> in_array($method, $i->method()));

        foreach ($routes as $route) {
            if ($route->matches($uri, $method)){
                return $route;
            }
       }
        return false;

    }

    /**
     * Performs route search based on the REQUEST_URI and REQUEST_METHOD variables of the server
     *
     * @throws InvalidFunctionParametersException
     * @throws InvalidUrlParametersException
     * @throws RouteNotFoundException
     * @throws ReflectionException
     * @return mixed
     */
    public function done(): mixed
    {
        $uri    =   $_SERVER['REQUEST_URI'];
        $method =   $_SERVER['REQUEST_METHOD'];
        return $this->doneWithParameters($uri, $method);
    }

    /**
     * Performs route search based on the uri and method parameters variables of the server
     *
     * @param string $uri
     * @param string $method
     * @return mixed
     * @throws InvalidFunctionParametersException
     * @throws InvalidUrlParametersException
     * @throws ReflectionException
     * @throws RouteNotFoundException
     */
    public function doneWithParameters(string $uri, string $method): mixed
    {
        $uri    =   $this->cleanRequestUri($uri);
        if ($route = $this->match($uri, $method)){
            /** Implement a logic before */
            return $route->dispatch($uri, $method);
        }

        throw new RouteNotFoundException($uri);
    }


    /**
     * Searches for a route by name, returns the route otherwise finds returns null
     *
     * @param string $name
     * @return Route|null
     */
    public function hasRouteByName(string $name): Route|null
    {
        $routes =   $this->collection->all();

        foreach ($routes as $route) {
            if ($route->name() === $name){
                return $route;
            }
        }

        return null;
    }

    /**
     * Searches for a route by uri, returns the route otherwise finds returns null
     *
     * @param string $uri
     * @return Route|null
     */
    public function hasRouteByUri(string $uri): Route|null
    {
        $routes =   $this->collection->all();
        $uri    =   $this->cleanRequestUri($uri);

        foreach ($routes as $route) {
            if ($route->uri() === $uri){
                return $route;
            }
        }

        return null;
    }

    /**
     * List all registered routes
     *
     * @return Router[]
     */
    public function listRoutes(): array
    {
        return $this->collection->all();
    }
    
}