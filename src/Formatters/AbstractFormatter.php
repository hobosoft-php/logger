<?php

namespace Library\Logger\Formatters;

use Library\Logger\Contracts\Handlers\FormatterInterface;
use Library\Logger\LogItem;

abstract class AbstractFormatter implements FormatterInterface
{
    abstract public function format(LogItem $item): mixed;
    
    public function formatBatch(array $items): array
    {
        foreach ($items as $k => $v)
            $items[$k] = $this->format($v);
        return $items;
    }
}
