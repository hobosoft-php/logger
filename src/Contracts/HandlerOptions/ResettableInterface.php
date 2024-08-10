<?php

namespace Hobosoft\Logger\Contracts\HandlerOptions;

interface ResettableInterface
{
    public function reset(): void;
}