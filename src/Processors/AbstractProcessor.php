<?php

namespace Library\Logger\Processors;

use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\LogItem;

abstract class AbstractProcessor implements ProcessorInterface
{
    abstract public function process(LogItem $item): ?LogItem;

    public function processBatch(array $items): array
    {
        foreach ($items as $k => $v)
            $items[$k] = $this->process($v);
        return $items;
    }
}