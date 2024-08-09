<?php

namespace Library\Logger\Contracts\Handlers;

use Library\Logger\Contracts\HandlerOptions\ClosableInterface;
use Library\Logger\LogItem;

interface ProcessorInterface
{
    /**
     * Process a log record.
     *
     * @param  LogItem   $item A record to format
     */
    public function process(LogItem $item): ?LogItem;
    
    /**
     * Process a batch of log record.
     *
     * @param  array   $items A record to format
     */
    public function processBatch(array $items): ?array;
}
