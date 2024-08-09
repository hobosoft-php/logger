<?php

namespace Library\Logger\Filters;

use Library\Logger\Contracts\HandlerOptions\AcceptableInterface;
use Library\Logger\Contracts\Handlers\FilterInterface;
use Library\Logger\Contracts\LogLevel;
use Library\Logger\LogItem;

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