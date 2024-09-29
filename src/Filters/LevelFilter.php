<?php

namespace Hobosoft\Logger\Filters;

use Hobosoft\Logger\Contracts\HandlerOptions\AcceptableInterface;
use Hobosoft\Logger\Contracts\Handlers\FilterInterface;
use Hobosoft\Logger\Contracts\LogLevel;
use Hobosoft\Logger\LogItem;

class LevelFilter implements FilterInterface
{
    public function __construct(
        protected int|string|LogLevel $minLevel = LogLevel::Debug,
        protected int|string|LogLevel $maxLevel = LogLevel::Emergency,
    )
    {
        $this->minLevel = LogLevel::fromMixed($minLevel);
        $this->maxLevel = LogLevel::fromMixed($maxLevel);
    }
    
    public function accept(LogItem $item): bool
    {
        return ($item->level->value >= $this->minLevel->value) && ($item->level->value <= $this->maxLevel->value);
    }
}