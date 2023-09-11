<?php

namespace Yolanda\Routing\Trait;

trait RouteFunctions
{
    private function cleanRequestUri(string $requestUri): string {


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
        if (str_starts_with($segment, '{') && str_ends_with($segment, '}')){
            return trim($segment, '{}');
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
    
}