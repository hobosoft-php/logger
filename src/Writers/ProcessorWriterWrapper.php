<?php

namespace Hobosoft\Logger\Writers;

use Closure;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Hobosoft\Logger\LogItem;

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