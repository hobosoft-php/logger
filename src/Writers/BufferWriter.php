<?php

namespace Library\Logger\Writers;

use Library\Logger\Contracts\HandlerOptions\FormattableInterface;
use Library\Logger\Contracts\HandlerOptions\ResettableInterface;
use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\CascadeInputTrait;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\Contracts\Traits\CascadeNameTrait;
use Library\Logger\Contracts\Traits\FormattableHandlerTrait;
use Library\Logger\LogItem;

class BufferWriter extends AbstractWriter implements WriterInterface, ResettableInterface
{
    use FormattableHandlerTrait;
    use CascadeOutputSingleTrait {
        CascadeOutputSingleTrait::__construct as __traitConstruct;
    }

    protected array $items = [];
    protected int $itemCount = 0;

    public function __construct(
        string $name,
        protected int $maxLines = 25,
    )
    {
        $this->__traitConstruct($name);
    }

    public function handle(LogItem $item): bool
    {
        $record = $this->formatRecord($item);
        $this->items[] = $record;
        $this->itemCount++;
        if ($this->itemCount >= $this->maxLines) {
            $this->flush();
        }
        return true;
    }

    public function flush(): void
    {
        if($this->items !== []) {
            if($this->hasOutputDestination()) {
                $this->getOutputDestination()->handleBatch($this->items);
                $this->clear();
            }
        }
    }

    public function close(): void
    {
        $this->flush();
        if($this->hasOutputDestination()) {
            $this->getOutputDestination()->close();
        }
    }

    public function clear(): void
    {
        $this->items = [];
        $this->itemCount = 0;
    }

    public function reset(): void
    {
        if ($this->hasOutputDestination() && $this->getOutputDestination() instanceof ResettableInterface) {
            $this->getOutputDestination()->reset();
        }
        $this->flush();
    }
}
