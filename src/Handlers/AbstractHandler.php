<?php

namespace Hobosoft\Logger\Handlers;

use Hobosoft\Logger\Contracts\HandlerOptions\CascadableOutputSingleInterface;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeNameTrait;
use Hobosoft\Logger\LogItem;

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