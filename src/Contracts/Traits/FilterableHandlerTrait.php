<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\Contracts\Handlers\FilterInterface;
use Hobosoft\Logger\LogItem;

trait FilterableHandlerTrait
{
    protected array $filters = [];

    public function pushFilter(FilterInterface|callable $callback): self
    {
        array_unshift($this->filters, $callback);
        return $this;
    }

    public function popFilter(): callable
    {
        if (\count($this->filters) === 0) {
            throw new \LogicException('You tried to pop from an empty filter stack.');
        }
        return array_shift($this->filters);
    }

    public function accept(LogItem $item): bool
    {
        foreach ($this->filters as $k => $v) {
            if ($v instanceof \Closure) {
                $this->filters[$k] = ($v = $v());
            }
            if ($v->accept($item) === false) {
                return false;
            }
        }
        return true;
    }
}
