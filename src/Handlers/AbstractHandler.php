<?php

namespace Library\Logger\Handlers;

use Library\Logger\Contracts\HandlerOptions\CascadableOutputSingleInterface;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\Contracts\Traits\CascadeNameTrait;
use Library\Logger\LogItem;

abstract class AbstractHandler implements HandlerInterface
{
    abstract public function handle(LogItem $item): bool;
    
    public function handleBatch(array $items): void
    {
        foreach ($items as $item) {
            if(is_string($item)) {
                print("string.\n");
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            }
            $this->handle($item);
        }
    }

    public function asHandler(): HandlerInterface
    {
        return $this;
    }
}