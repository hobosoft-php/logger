<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\HandlerOptions\CascadableOutputSingleInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\FormattableHandlerTrait;
use Hobosoft\Logger\Handlers\AbstractHandler;
use Hobosoft\Logger\LogItem;

abstract class AbstractWriter extends AbstractHandler implements WriterInterface, CascadableOutputSingleInterface
{
    use FormattableHandlerTrait;

    public function close(): void
    {
    }
}
