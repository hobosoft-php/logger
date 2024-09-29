<?php

namespace Hobosoft\Logger\Formatters;

use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;
use Hobosoft\Logger\LogItem;

class PassthruFormatter extends AbstractFormatter implements FormatterInterface
{
    public function format(LogItem $item): ?LogItem
    {
        return $item;
    }
}