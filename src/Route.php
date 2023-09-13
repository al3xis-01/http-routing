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
     * @var mixed
     */
    private  mixed $callback;

    /**
     * Args of callback action from Route
     *
     * @var array
     */
    private array $argsCallback;

    /**
     * @var array
     */
    private array $callbackInfo;

    /**
     * @param string $name
     * @param string $uri
     * @param array|string $method
     * @param array|callable|null|string $callback
     */
    public function __construct(string $name, string $uri, array | string $method,  array|callable|null|string $callback)
    {
        $uri = $this->cleanRequestUri($uri);
        $this->name = $name;
        $this->uri = $uri;
        $this->method = is_array($method) ? $method : [$method];
        $this->callback = $callback;
        $this->parametersWithValues = [];
        $this->argsCallback = [];
        $this->callbackInfo =   [];


        $this->parameters   =   $this->extractParametersFromUri($uri);
        $this->callbackInfo =   $this->parseCallbackInfo($callback);

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
     * @param bool|string $parameterName
     * @param mixed $value
     * @return void
     */
    public function setParameterValue(bool|string $parameterName, mixed $value): void
    {
        $this->parametersWithValues[$parameterName] = $value;
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
                    $this->setParameterValue($parameterName, $uriRequestSegments[$i]);
                    continue;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     * @throws InvalidFunctionParametersException
     * @throws InvalidUrlParametersException
     * @throws ReflectionException
     */
    public function dispatch(): mixed
    {

        $this->prepareCallback();
        $this->setArgsCallback();

        $result = $this->runCallback();

        return $result;
    }

    /**
     * Sets the arguments for the callback based on request parameters.
     *
     * @throws InvalidUrlParametersException if the number of URL parameters doesn't match the callback.
     * @throws ReflectionException if there is an issue with obtaining callback reflection.
     * @throws InvalidFunctionParametersException if callback parameters are missing in the request.
     */
    private function setArgsCallback(): void
    {
        $callable = $this->callback;
        $this->argsCallback =   [];

        $filteredRequestParams = array_filter($this->parametersWithValues, fn ($param) => $param !== null);
        $info       =   $this->callbackInfo;

        switch ($info['type']){
            case self::$IS_ARRAY_OBJECT:
            case self::$IS_ARRAY_NAMESPACE_CLASS:
            case self::$IS_NAMESPACE_STRING_AND_METHOD:
                $reflection =   new \ReflectionClass($info['class']);
                $parameters =   $reflection->getMethod($info['method'])->getParameters();
                break;
            case self::$IS_CALLBACK:
                $reflection = new ReflectionFunction($callable);
                $parameters = $reflection->getParameters();
                break;
            default:
                throw new InvalidFunctionParametersException('');
        }

        $filteredFunctionParams = array_filter($parameters, fn ($param) =>
            !$param->allowsNull() && !$param->isOptional() && !$param->isDefaultValueAvailable()
        );

        if (count($filteredFunctionParams) === 0){
            return;
        }

        if (count($filteredFunctionParams) > count($filteredRequestParams)) {
            /*IMPLEMENT LOGIC FOR BODY DATA*/
            throw new InvalidUrlParametersException($this->name);
        }

        foreach ($filteredFunctionParams as $param) {
            if (!array_key_exists($param->name, $filteredRequestParams)) {
                throw new InvalidFunctionParametersException($reflection->name, $param->name);
            }
            $this->argsCallback[]   =   $filteredRequestParams[$param->name];
        }

    }


    /**
     * Verifies that the provided callback is callable.
     *
     */
    private function prepareCallback(): void
    {
        $callable   =   $this->callback;
        $info       =   $this->parseCallbackInfo($callable);
        $this->callbackInfo =   $info;

    }

    /**
     * Calls the stored callback function with the provided arguments.
     *
     * @return mixed The result of the callback function.
     * @throws InvalidFunctionParametersException
     */
    private function runCallback(): mixed
    {
        $callback = $this->callback;
        $args = $this->argsCallback;

        $info   =   $this->callbackInfo;
        switch ($info['type']){
            case self::$IS_ARRAY_OBJECT:
            case self::$IS_ARRAY_NAMESPACE_CLASS:
            case self::$IS_NAMESPACE_STRING_AND_METHOD:
                $result = call_user_func_array([$info['object'], $info['method']], $args);
                break;
            case self::$IS_CALLBACK:
                $result = call_user_func_array($callback, $args);
                break;
            default:
                throw new InvalidFunctionParametersException('', '');
        }

        return $result;
    }


}