<?php

namespace Yolanda\Http\Routing;

use SplObjectStorage;

/**
 * Represents a collection of Route objects.
 *
 */
class RouteCollection
{
    /**
     * Collection
     *
     * @var SplObjectStorage
     */
    private SplObjectStorage $storage;

    /**
     * Initializes a new RouteCollection.
     */
    public function __construct()
    {
        $this->storage  =   new SplObjectStorage();
    }

    /**
     * Attach a Route to the collection.
     *
     * @param Route $route
     * @return $this
     */
    public function attach(Route $route): self
    {
        $this->storage->attach($route, null);

        return $this;
    }


    /**
     * Fetch all routes stored on this collection of routes and return it.
     *
     * @return Route[]
     */
    public function all(): array
    {
        $routes =   [];
        foreach ($this->storage as $value) {
            $routes[]   =   $value;
        }

        return $routes;
    }
}