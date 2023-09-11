<?php

namespace Yolanda\Routing\Exceptions;

class InvalidFunctionParametersException extends \Exception
{
    public function __construct(string $function) {
        parent::__construct(sprintf('Function %s parameters do not match callback parameters.', $function));
    }
}