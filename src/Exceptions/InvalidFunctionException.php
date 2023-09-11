<?php

namespace Yolanda\Routing\Exceptions;

class InvalidFunctionException extends \Exception
{
    public function __construct(string $functionNmae) {
        parent::__construct(sprintf('Invalid function with name %s.', $functionNmae));
    }
}