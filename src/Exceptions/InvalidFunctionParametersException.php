<?php

namespace Yolanda\Routing\Exceptions;

class InvalidFunctionParametersException extends \Exception
{
    public function __construct(string $function, string $nameParameter = '') {
        parent::__construct(sprintf('Function %s parameters (%s) do not match callback parameters (%s).', $function, $nameParameter, ''));
    }
}