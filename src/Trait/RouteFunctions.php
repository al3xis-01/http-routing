<?php

namespace Yolanda\Http\Routing\Trait;

trait RouteFunctions
{

    public static int $IS_ARRAY_OBJECT   =   1;
    public static int $IS_ARRAY_NAMESPACE_CLASS   =   2;
    public static int $IS_NAMESPACE_STRING_AND_METHOD   =   3;
    public static int $IS_CALLBACK   =   4;
    public static int $IS_UNKNOWN   =   -1;

    private function cleanRequestUri(string $requestUri): string {

        $requestUri =   rawurldecode($requestUri);

        if (str_starts_with($requestUri, '/')) {
            $requestUri = substr($requestUri, 1);
        }


        if (str_ends_with($requestUri, '/')) {
            $requestUri = substr($requestUri, 0, -1);
        }

        return $requestUri;
    }

    private function splitUri(string $uri): array
    {
        return explode('/', trim($uri, '/'));
    }


    private function getParameterFromSegment(string $segment): bool | string
    {
        $segment    =   rawurldecode($segment);
        if (str_starts_with($segment, '{') && str_ends_with($segment, '}')){
            return trim($segment, '{}');
        }

        if (str_starts_with($segment, ':')){
            return trim($segment, ':');
        }

        return false;
    }

    private function extractParametersFromUri(string $uri): array
    {
        $uri    =   $this->cleanRequestUri($uri);
        $uriSegments = $this->splitUri($uri);
        $parameters   =   [];

        foreach ($uriSegments as $segment) {
            if ($parameterName = $this->getParameterFromSegment($segment)) {
                $parameters[] = $parameterName;
            }
        }

        return $parameters;
    }

    private function parseCallbackInfo( array|callable|null|string $callback): array
    {
        $info   =   [];
        $info['type']   =   self::$IS_UNKNOWN;

        if (is_array($callback) && count($callback) === 2){
            $resource   =   $callback[0];
            $method     =   $callback[1];

            if (is_object($resource)){
                $info['type']       =   self::$IS_ARRAY_OBJECT;
                $info['description'] = 'Array object';
                $info['class']  =   get_class($resource);
                $info['object'] =   $resource;
                $info['method'] =   $method;
                return $info;

            }

            if (is_string($resource)){
                $info['type']           =   self::$IS_ARRAY_NAMESPACE_CLASS;
                $info['description']   =   'Array namespace class';
                $info['class']  =   $resource;
                $info['object'] =   new $resource();
                $info['method'] =   $method;
                return $info;
            }
        }

        if (is_string($callback) && str_contains($callback, '::')){
            list($class, $method)   =   explode('::', $callback);

            $info['type']           =   self::$IS_NAMESPACE_STRING_AND_METHOD;
            $info['description']   =   'Namespace string and method';
            $info['class']  =   $class;
            $info['object'] =   new $class();
            $info['method'] =   $method;
            return $info;
        }

        if (is_callable($callback)){
            $info['type']           =   self::$IS_CALLBACK;
            $info['description']   =   'Anonymous function or callback';
            return $info;
        }



        return $info;
    }
    
}