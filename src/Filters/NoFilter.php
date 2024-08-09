<?php

namespace Library\Logger\Filters;

use Library\Logger\Contracts\Handlers\FilterInterface;
use Library\Logger\LogItem;

class NoFilter implements FilterInterface
{
    public function accept(LogItem $item): bool
    {
        return true;
    }
}