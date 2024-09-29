<?php

namespace Hobosoft\Logger\Formatters;

use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;
use Hobosoft\Logger\LogItem;

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
