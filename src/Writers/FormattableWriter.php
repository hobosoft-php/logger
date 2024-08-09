<?php

namespace Library\Logger\Writers;

use Library\Logger\Contracts\HandlerOptions\FormattableInterface;
use Library\Logger\Contracts\HandlerOptions\ResettableInterface;
use Library\Logger\Contracts\Handlers\FormatterInterface;
use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\FormattableHandlerTrait;
use Library\Logger\LogItem;

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