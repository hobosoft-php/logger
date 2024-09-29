<?php

namespace Hobosoft\Logger\Handlers;

use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\LogItem;

class EntrypointHandler extends AbstractHandler implements HandlerInterface
{
    public function handle(LogItem $item): bool
    {
        return true;
    }
}
