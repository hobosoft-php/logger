<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\Contracts\Traits\FormattableHandlerTrait;
use Hobosoft\Logger\LogItem;

class PrintWriter extends AbstractWriter implements WriterInterface
{
    use FormattableHandlerTrait;
    use CascadeOutputSingleTrait;
    
    public function handle(LogItem $item): bool
    {
        print($this->formatRecord($item));
        return true;
    }
    
    public function close(): void
    {
    }
}