<?php

namespace Library\Logger\Contracts\Handlers;

use Library\Logger\LogItem;

interface FormatterInterface
{
    /**
     * Process a log record.
     *
     * @param  LogItem   $item A record to format
     */
    public function format(LogItem $item): mixed;
    
    /**
     * Process a batch of log record.
     *
     * @param  array   $items A record to format
     */
    public function formatBatch(array $items): mixed;
}
