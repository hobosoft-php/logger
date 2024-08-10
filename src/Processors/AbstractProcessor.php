<?php

namespace Hobosoft\Logger\Processors;

use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\LogItem;

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