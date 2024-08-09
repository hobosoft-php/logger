<?php

namespace Library\Logger\Formatters;

use Library\Logger\Contracts\Handlers\FormatterInterface;
use Library\Logger\LogItem;

class PassthruFormatter extends AbstractFormatter implements FormatterInterface
{
    public function format(LogItem $item): ?LogItem
    {
        return $item;
    }
}