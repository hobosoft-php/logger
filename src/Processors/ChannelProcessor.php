<?php

namespace Library\Logger\Processors;

use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\LogItem;

class ChannelProcessor extends AbstractProcessor implements ProcessorInterface
{
    public function __construct(
        protected string $channel,
    ) {}

    public function process(LogItem $item): LogItem
    {
        $item->context['channel'] = $this->channel;
        return $item;
    }
}