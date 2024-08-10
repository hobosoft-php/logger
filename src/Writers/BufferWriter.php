<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\HandlerOptions\FormattableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeInputTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeNameTrait;
use Hobosoft\Logger\Contracts\Traits\FormattableHandlerTrait;
use Hobosoft\Logger\LogItem;

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
