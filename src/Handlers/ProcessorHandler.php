<?php

namespace Library\Logger\Handlers;

use Closure;
use Library\Logger\Contracts\HandlerOptions\CascadableOutputSingleInterface;
use Library\Logger\Contracts\HandlerOptions\ResettableInterface;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\Contracts\Traits\CascadeNameTrait;
use Library\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Library\Logger\LogItem;

class ProcessorHandler extends AbstractHandler implements HandlerInterface, ResettableInterface, CascadableOutputSingleInterface
{
    use ProcessableHandlerTrait;
    use CascadeOutputSingleTrait {
        CascadeOutputSingleTrait::__construct as __traitConstruct;
    }

    public function __construct(
        string $name,
        ProcessorInterface|Closure|array $processors = [],
    ) {
        $this->__traitConstruct($name);
        if (is_array($processors) === false) {
            $processor = [$processors];
        }
        foreach($processors as $p)
            $this->pushProcessor(($p instanceof Closure) ? $p : (fn() => $p));
    }
    public function handle(LogItem $item): bool
    {
        $this->getOutputDestination()->handle($this->processRecord($item));
        return true;
    }
    
    public function reset(): void
    {
        $this->resetProcessors();
    }
}