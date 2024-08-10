<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\Contracts\HandlerOptions\ClosableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\FlushableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\LogItem;

trait ProcessableHandlerTrait
{
    /**
     * @var callable[]
     * @phpstan-var ProcessorInterface
     */
    protected array $processors = [];
    
    protected function processRecord(LogItem $item): LogItem
    {
        foreach ($this->processors as $k => $v) {
            if ($v instanceof \Closure) {
                $this->processors[$k] = ($v = $v());
            } elseif (is_array($v)) {
                print(".");
            }
            $item = $v->process($item);
        }
        return $item;
    }
    
    /**
     * @inheritDoc
     */
    public function pushProcessor(ProcessorInterface|callable|array $callback): self
    {
        if (is_array($callback) && count($callback) == 0) {
            return $this;
        }
        array_unshift($this->processors, $callback);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function popProcessor(): ProcessorInterface|callable
    {
        if (\count($this->processors) === 0) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }
        return array_shift($this->processors);
    }
    
    protected function resetProcessors(): void
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof ResettableInterface) {
                $processor->reset();
            }
        }
    }
    
    protected function flushProcessors(): void
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof FlushableInterface) {
                $processor->flush();
            }
        }
    }
    
    protected function closeProcessors(): void
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof ClosableInterface) {
                $processor->close();
            }
        }
    }
}