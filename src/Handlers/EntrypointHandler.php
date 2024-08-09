<?php

namespace Library\Logger\Handlers;

use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\LogItem;

class EntrypointHandler extends AbstractHandler implements HandlerInterface
{
    public function handle(LogItem $item): bool
    {
        return true;
    }
}
