<?php

namespace Library\Logger\Writers;

use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\Contracts\Traits\CascadeNameTrait;
use Library\Logger\LogItem;

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