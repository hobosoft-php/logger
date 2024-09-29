<?php

namespace Hobosoft\Logger\Contracts\HandlerOptions;

use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;

interface ProcessableInterface
{
    public function pushProcessor(ProcessorInterface|callable|array $callback): self;
    public function popProcessor(): ProcessorInterface|callable;
}
