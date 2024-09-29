<?php

namespace Hobosoft\Logger\Processors;

use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\LogItem;

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