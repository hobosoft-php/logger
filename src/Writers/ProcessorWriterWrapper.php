<?php

namespace Library\Logger\Writers;

use Closure;
use Library\Logger\Contracts\HandlerOptions\ResettableInterface;
use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Library\Logger\LogItem;

class ProcessorWriterWrapper extends WriterWrapper implements WriterInterface, ResettableInterface
{
    use ProcessableHandlerTrait;

    public function __construct(
        protected WriterInterface $writer,
        ProcessorInterface|Closure|array $processor = [],
    )
    {
        parent::__construct($writer);
        if (is_array($processor) === false) {
            $processor = [$processor];
        }
        foreach($processor as $p) {
            $this->pushProcessor(($p instanceof Closure) ? $p : (fn() => $p));
        }
    }

    public function handle(LogItem $record): bool
    {
        if ($this->writer instanceof NullWriter) {
            die("Writer has been detached.");
        }
        return $this->writer->handle($this->processRecord($record));
    }
}