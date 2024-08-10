<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeNameTrait;
use Hobosoft\Logger\LogItem;

class PrintWriter extends AbstractWriter implements WriterInterface
{
    use CascadeOutputSingleTrait;
    
    public function handle(LogItem $item): bool
    {
        print((string)$item);
        return true;
    }
    
    public function close(): void
    {
    }
}