<?php

namespace Yolanda\Http\Routing\Exceptions;

class RouteNotFoundException extends \Exception
{
    public function __construct($requestedRoute) {

        $message = "The requested route '$requestedRoute' was not found.";
        parent::__construct($message);
    }
}