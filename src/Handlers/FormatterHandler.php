<?php

namespace Hobosoft\Logger\Handlers;

use Closure;
use Hobosoft\Logger\Contracts\HandlerOptions\ClosableInterface;
use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\Traits\FormattableHandlerTrait;
use Hobosoft\Logger\LogItem;

class FormatterHandler extends AbstractHandler implements HandlerInterface
{
    use FormattableHandlerTrait;
    
    public function __construct(
        FormatterInterface $formatter
    ) {
        $this->setFormatter($formatter);
    }
    
    public function handle(LogItem $item): bool
    {
        $this->formatRecord($item);
        return true;
    }
}