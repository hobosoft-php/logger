<?php

namespace Library\Logger\Writers;

use Closure;
use Library\Logger\Contracts\HandlerOptions\ResettableInterface;
use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\Contracts\Traits\CascadeNameTrait;
use Library\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Library\Logger\LogItem;

class ProcessorWriter extends AbstractWriter implements WriterInterface
{
    use CascadeOutputSingleTrait;
    use ProcessableHandlerTrait;

    public function __construct(
        WriterInterface $writer,
        ProcessorInterface|Closure|array $processor = [],
    )
    {
        if (is_array($processor) === false) {
            $processor = [$processor];
        }
        foreach($processor as $p)
            $this->pushProcessor(($p instanceof Closure) ? $p : (fn() => $p));
        $this->setOutputDestination($writer);
    }

    public function handle(LogItem $item): bool
    {
        return $this->getOutputDestination()->handle($this->processRecord($item));
    }
    
    public function close(): void
    {
    }
}