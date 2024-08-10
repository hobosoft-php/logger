<?php

namespace Hobosoft\Logger\Filters;

use Hobosoft\Logger\Contracts\Handlers\FilterInterface;
use Hobosoft\Logger\LogItem;

class ChannelFilter implements FilterInterface
{
    public function __construct(
        protected string|array $channels = [],
        protected bool $excludeChannelsInArray = false,
    )
    {
        if(is_string($channels)) {
            $this->channels = [$channels];
        }
    }

    public function accept(LogItem $item): bool
    {
        $ch = $item->context['channel'] ?? 'default';
        if($this->excludeChannelsInArray) {
            return !in_array($ch, $this->channels);
        }
        return in_array($ch, $this->channels);
    }
}