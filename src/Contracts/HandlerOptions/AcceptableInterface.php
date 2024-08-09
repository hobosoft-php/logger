<?php

namespace Library\Logger\Contracts\HandlerOptions;

use Library\Logger\LogItem;

interface AcceptableInterface
{
    /**
     * Checks whether the given record will be handled by this handler.
     *
     * This is mostly done for performance reasons, to avoid calling processors for nothing.
     *
     * Handlers should still check the record levels within handle(), returning false in isHandling()
     * is no guarantee that handle() will not be called, and isHandling() might not be called
     * for a given record.
     *
     * @param LogItem $item Partial log record having only a level initialized
     */
    public function accept(LogItem $item): bool;
}