<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\HandlerOptions\FormattableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\FormattableHandlerTrait;
use Hobosoft\Logger\LogItem;

class FormattableWriter extends AbstractWriter implements WriterInterface, ResettableInterface
{
    use FormattableHandlerTrait;

    public function __construct(
        protected WriterInterface     $writer,
        ?FormatterInterface $formatter = null,
    )
    {
        $this->formatter = $formatter;
    }

    public function handle(LogItem $item): bool
    {
        $r = $this->getFormatter()->format($item);
        $item->formatted = $r;
        return $this->writer->handle($item);
    }

    public function reset(): void
    {
        if ($this->writer instanceof ResettableInterface) {
            $this->writer->reset();
        }
    }
    
    public function close(): void
    {
    }
}