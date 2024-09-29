<?php

namespace Hobosoft\Logger\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}