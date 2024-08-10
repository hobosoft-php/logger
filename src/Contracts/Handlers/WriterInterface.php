<?php

namespace Hobosoft\Logger\Contracts\Handlers;

use Closure;
use Hobosoft\Logger\Contracts\HandlerOptions\ClosableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\FlushableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\FormattableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\LogItem;

interface WriterInterface extends HandlerInterface, ClosableInterface, FormattableInterface
{
    public function handle(LogItem $item): bool;
    public function handleBatch(array $items): void;
    public function close(): void;
    public function setFormatter(FormatterInterface|Closure $formatter): self;
    public function getFormatter(): FormatterInterface|Closure;
}
