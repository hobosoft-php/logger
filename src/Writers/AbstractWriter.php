<?php

namespace Library\Logger\Writers;

use Library\Logger\Contracts\HandlerOptions\CascadableOutputSingleInterface;
use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\FormattableHandlerTrait;
use Library\Logger\Handlers\AbstractHandler;
use Library\Logger\LogItem;

abstract class AbstractWriter extends AbstractHandler implements WriterInterface, CascadableOutputSingleInterface
{
    use FormattableHandlerTrait;

    public function close(): void
    {
    }
}
