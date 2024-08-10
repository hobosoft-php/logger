<?php

namespace Hobosoft\Logger\Handlers;

use Closure;
use Hobosoft\Logger\Contracts\HandlerOptions\CascadableOutputSingleInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeNameTrait;
use Hobosoft\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Hobosoft\Logger\LogItem;

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