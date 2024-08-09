<?php

namespace Library\Logger\Contracts\HandlerOptions;

use Library\Logger\Contracts\Handlers\ProcessorInterface;

interface ProcessableInterface
{
    public function pushProcessor(ProcessorInterface|callable|array $callback): self;
    public function popProcessor(): ProcessorInterface|callable;
}
