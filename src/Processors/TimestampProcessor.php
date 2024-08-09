<?php

namespace Library\Logger\Processors;

use DateTimeImmutable;
use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\LogItem;

class TimestampProcessor extends AbstractProcessor implements ProcessorInterface
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s'; //'Y-m-d H:i:s.v'
    
    public function __construct(private bool $asUnixTimestamp = false) {}

    public function process(LogItem $item): LogItem
    {
        $item->context['timestamp'] = $this->formatTimestamp('now');//intval(microtime(true) * 1000000);
        return $item;
    }

    public function formatTimestamp(int|string $ts): string
    {
        if ($this->asUnixTimestamp) {
            return $ts;
        }
        return (new DateTimeImmutable($ts))->format(self::DATETIME_FORMAT);
    }
}
