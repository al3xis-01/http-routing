<?php

namespace Yolanda\Http\Routing\Exceptions;

class InvalidNameForRouteException extends \Exception
{
    public function __construct() {
        parent::__construct('Invalid name for route.');
    }
}