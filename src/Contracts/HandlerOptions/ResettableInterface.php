<?php

namespace Library\Logger\Contracts\HandlerOptions;

interface ResettableInterface
{
    public function reset(): void;
}