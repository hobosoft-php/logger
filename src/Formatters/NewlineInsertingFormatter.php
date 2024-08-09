<?php

namespace Library\Logger\Formatters;

use Library\Logger\Contracts\Handlers\FormatterInterface;
use Library\Logger\LogItem;

class NewlineInsertingFormatter extends AbstractFormatter implements FormatterInterface
{
    public function format(LogItem $item): ?LogItem
    {
        $item->formatted = ($item) . "\n";
        return $item;
    }
}