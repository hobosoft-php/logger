<?php

namespace Hobosoft\Logger\Formatters;

use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;
use Hobosoft\Logger\LogItem;

class NewlineInsertingFormatter extends AbstractFormatter implements FormatterInterface
{
    public function format(LogItem $item): ?LogItem
    {
        $item->formatted = ($item) . "\n";
        return $item;
    }
}