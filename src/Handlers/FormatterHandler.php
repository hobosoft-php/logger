<?php

namespace Library\Logger\Handlers;

use Closure;
use Library\Logger\Contracts\HandlerOptions\ClosableInterface;
use Library\Logger\Contracts\Handlers\FormatterInterface;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Contracts\Traits\FormattableHandlerTrait;
use Library\Logger\LogItem;

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