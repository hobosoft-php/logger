<?php

namespace Hobosoft\Logger\Filters;

use Hobosoft\Logger\Contracts\Handlers\FilterInterface;
use Hobosoft\Logger\LogItem;

class NoFilter implements FilterInterface
{
    public function accept(LogItem $item): bool
    {
        return true;
    }
}