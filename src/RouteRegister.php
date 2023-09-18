<?php

namespace Yolanda\Http\Routing;

/**
 *
 */
class RouteRegister
{

    /**
     * @var string
     */
    private string $uri;
    /**
     * @var string
     */
    private string $name;
    /**
     * @var array
     */
    private array $method;
    /**
     * @var mixed
     */
    private mixed $callback;
    /**
     * @var RouteCollection
     */
    private RouteCollection $collection;

    /**
     * @param RouteCollection $collection
     */
    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
        $this->init();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $uri
     * @param array|callable|string|null $callback
     * @return $this
     */
    public function get(string $uri, array|callable|null|string $callback): self
    {
        return $this->addRoute($uri, 'GET', $callback);
    }

    /**
     * @param string $uri
     * @param array|callable|string|null $callback
     * @return $this
     */
    public function post(string $uri, array|callable|null|string $callback): self
    {
        return $this->addRoute($uri, 'POST', $callback);
    }

    /**
     * @param string $uri
     * @param array|callable|string|null $callback
     * @return $this
     */
    public function put(string $uri, array|callable|null|string $callback): self
    {
        return $this->addRoute($uri, 'PUT', $callback);
    }

    /**
     * @param string $uri
     * @param array|callable|string|null $callback
     * @return $this
     */
    public function delete(string $uri, array|callable|null|string $callback): self
    {
        return $this->addRoute($uri, 'DELETE', $callback);
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array|callable|string|null $callback
     * @return $this
     */
    public function addRoute(string $uri, string $method, array|callable|null|string $callback): self
    {
        if (in_array($method, $this->method)){
            return $this;
        }

        $this->uri  =   $uri;
        $this->method[]   =   $method;
        $this->callback =   $callback;

        return $this;
    }

    /**
     * @return void
     */
    public function done(): void
    {
        $route  =   new Route(
            $this->name,
            $this->uri,
            $this->method,
            $this->callback
        );
        $this->collection->attach($route);
        $this->init();
    }


    /**
     * @return void
     */
    private function init(): void
    {
        $this->uri  =   '';
        $this->name =   '';
        $this->method  =   [];
        $this->callback =   null;
    }
}