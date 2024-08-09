<?php

namespace Library\Logger\Contracts\Handlers;

use Closure;
use Library\Logger\Contracts\HandlerOptions\ClosableInterface;
use Library\Logger\Contracts\HandlerOptions\FlushableInterface;
use Library\Logger\Contracts\HandlerOptions\FormattableInterface;
use Library\Logger\Contracts\HandlerOptions\ResettableInterface;
use Library\Logger\LogItem;

interface WriterInterface extends HandlerInterface, ClosableInterface, FormattableInterface
{
    public function handle(LogItem $item): bool;
    public function handleBatch(array $items): void;
    public function close(): void;
    public function setFormatter(FormatterInterface|Closure $formatter): self;
    public function getFormatter(): FormatterInterface|Closure;
}
