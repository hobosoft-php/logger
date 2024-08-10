<?php

namespace Hobosoft\Logger\Writers;

use Closure;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\FilterInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\LogItem;

class FilterWriterWrapper extends WriterWrapper implements WriterInterface, ResettableInterface
{
    public function __construct(
        protected WriterInterface $writer,
        protected FilterInterface|Closure $filter,
    ) {
        parent::__construct($this->writer);
    }

    public function handle(LogItem $record): bool
    {
        if ($this->getFilter()->accept($record)) {
            return parent::handle($record);
        }
        return true;
    }

    private function getFilter(): FilterInterface
    {
        if (!$this->filter instanceof FilterInterface) {
            if (!(($filter = ($this->filter)()) instanceof FilterInterface)) {
                throw new \RuntimeException("The factory Closure should return a FilterInterface");
            }
            $this->filter = $filter;
        }

        return $this->filter;
    }
}