<?php

namespace Yolanda\Http\Routing\Exceptions;

class InvalidUrlParametersException extends \Exception
{
    public function __construct(string $route) {
        parent::__construct(sprintf('URL %s parameters do not match callback parameters.', $route));
    }
}