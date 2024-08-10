<?php

namespace Hobosoft\Logger\Writers;

use Closure;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeNameTrait;
use Hobosoft\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Hobosoft\Logger\LogItem;

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