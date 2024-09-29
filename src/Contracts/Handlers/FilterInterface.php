<?php

namespace Hobosoft\Logger\Contracts\Handlers;

use Hobosoft\Logger\Contracts\HandlerOptions\AcceptableInterface;
use Hobosoft\Logger\LogItem;

interface FilterInterface extends AcceptableInterface
{
    public function accept(LogItem $item): bool;
}
