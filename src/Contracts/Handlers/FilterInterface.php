<?php

namespace Library\Logger\Contracts\Handlers;

use Library\Logger\Contracts\HandlerOptions\AcceptableInterface;
use Library\Logger\LogItem;

interface FilterInterface extends AcceptableInterface
{
    public function accept(LogItem $item): bool;
}
