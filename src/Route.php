<?php

namespace Yolanda\Routing;

use ReflectionException;
use ReflectionFunction;
use Yolanda\Routing\Exceptions\InvalidFunctionException;
use Yolanda\Routing\Exceptions\InvalidFunctionParametersException;
use Yolanda\Routing\Exceptions\InvalidUrlParametersException;
use Yolanda\Routing\Trait\RouteFunctions;

/**
 * Represents a route definition used in a routing system.
 * This class holds information about the URI pattern, HTTP methods, callback, and associated parameters.
 */
class Route
{
    use RouteFunctions;

    /**
     * Uri from Route
     *
     * @var string
     */
    private string $uri;

    /**
     * Methods Http from Route
     *
     * @var array
     */
    private array $method;

    /**
     * Name from Route
     *
     * @var string
     */
    private string $name;

    /**
     * Parameters from Route
     *
     * @var array
     */
    private array $parameters;

    /**
     * Parameters with values from Route
     *
     * @var array
     */
    private array $parametersWithValues;

    /**
     * Callback or action from Route
     *
     * @var mixed|callable
     */
    private mixed $callback;

    /**
     * Args of callback action from Route
     *
     * @var array
     */
    private array $argsCallback;

    /**
     * @param string $name
     * @param string $uri
     * @param array $method
     * @param callable $callback
     */
    public function __construct(string $name, string $uri, array $method, callable $callback)
    {
        $uri = $this->cleanRequestUri($uri);
        $this->name = $name;
        $this->uri = $uri;
        $this->method = $method;
        $this->callback = $callback;
        $this->parameters = $this->extractParametersFromUri($uri);
        $this->parametersWithValues = [];
        $this->argsCallback = [];

        foreach ($this->parameters as $parameter) {
            $this->parametersWithValues[$parameter] = null;
        }

    }

    /**
     * @return string
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function method(): array
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function parametersWithValues(): array
    {
        return $this->parametersWithValues;
    }


    /**
     * Checks if the provided URI and HTTP method match the route's criteria.
     *
     * @param string $uri The URI to match.
     * @param string $method The HTTP method to match.
     *
     * @return bool True if the URI and method match the route; otherwise, false.
     */
    public function matches(string $uri, string $method): bool
    {
        $uri = $this->cleanRequestUri($uri);

        if (false === in_array(strtoupper($method), $this->method)) {
            return false;
        }

        if ($uri === $this->uri) {
            return true;
        }

        $uriRequestSegments = $this->splitUri($uri);
        $uriSegments = $this->splitUri($this->uri);

        if (count($uriRequestSegments) !== count($uriSegments)) {
            return false;
        }

        for ($i = 0; $i < count($uriSegments); $i++) {

            if ($uriSegments[$i] === $uriRequestSegments[$i]) {
                continue;
            }

            if ($parameterName = $this->getParameterFromSegment($uriSegments[$i])) {
                if (in_array($parameterName, $this->parameters)) {
                    $this->parametersWithValues[$parameterName] = $uriRequestSegments[$i];
                    continue;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Sets the arguments for the callback based on request parameters.
     *
     * @throws InvalidUrlParametersException if the number of URL parameters doesn't match the callback.
     * @throws ReflectionException if there is an issue with obtaining callback reflection.
     * @throws InvalidFunctionParametersException if callback parameters are missing in the request.
     */
    public function setArgsCallback(): void
    {
        $callable = $this->callback;

        $reflection = new ReflectionFunction($callable);
        $parameters = $reflection->getParameters();

        $filteredFunctionParams = array_filter($parameters, fn ($param) =>
            !$param->allowsNull() && !$param->isOptional() && !$param->isDefaultValueAvailable()
        );

        $filteredRequestParams = array_filter($this->parametersWithValues, fn ($param) => $param !== null);

        if (count($filteredRequestParams) !== count($filteredFunctionParams)) {
            throw new InvalidUrlParametersException($this->name);
        }

        $this->argsCallback = [];

        if (empty($filteredFunctionParams) && empty($filteredRequestParams)) {
            return;
        }

        foreach ($filteredFunctionParams as $param) {
            if (!array_key_exists($param->name, $filteredRequestParams)) {
                throw new InvalidFunctionParametersException($reflection->name);
            }
        }

        $this->argsCallback = $filteredRequestParams;
    }


    /**
     * Verifies that the provided callback is callable.
     *
     * @throws InvalidFunctionException if the callback is not callable.
     */
    public function prepareCallback(): void
    {
        $callable   =   $this->callback;
        if (false === is_callable($callable)){
            throw new InvalidFunctionException($callable);
        }
    }

    /**
     * Calls the stored callback function with the provided arguments.
     *
     * @return mixed The result of the callback function.
     */
    public function runCallback(): mixed
    {
        $callback = $this->callback;
        $args = $this->argsCallback;

        $result = call_user_func_array($callback, $args);

        return $result;
    }


    /**
     * @throws InvalidFunctionException
     * @throws InvalidUrlParametersException
     * @throws ReflectionException
     * @throws InvalidFunctionParametersException
     */
    public function dispatch(): mixed
    {

        $this->prepareCallback();
        $this->setArgsCallback();

        $result = $this->runCallback();

        return $result;
    }

}