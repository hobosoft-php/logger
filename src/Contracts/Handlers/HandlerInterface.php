<?php

namespace Hobosoft\Logger\Contracts\Handlers;

use Hobosoft\Logger\LogItem;

interface HandlerInterface
{
    /**
     * Handles a record.
     *
     * All records may be passed to this method, and the handler should discard
     * those that it does not want to handle.
     *
     * The return value of this function controls the bubbling process of the handler stack.
     * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
     * calling further handlers in the stack with a given log record.
     *
     * @param  LogItem $item The record to handle
     * @return bool      true means that this handler handled the record, and that bubbling is not permitted.
     *                          false means the record was either not processed or that this handler allows bubbling.
     */
    public function handle(LogItem $item): bool;

    /**
     * Handles a set of records at once.
     *
     * @param array<LogItem> $items The records to handle
     */
    public function handleBatch(array $items): void;
}
