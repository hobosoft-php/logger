<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\CascadeBuilder;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\LogLevel;
use Hobosoft\Logger\Exceptions\HandlerDestinationAlreadySetException;
use Hobosoft\Logger\LogItem;
use Hobosoft\Logger\Writers\NullWriter;

trait CascadeOutputSingleTrait
{
    use CascadeNameTrait;
    use CascadeInputTrait;

    protected ?HandlerInterface $outputDestination = null;

    public function isCascadeEnd(): bool
    {
        return ($this->outputDestination === null);
    }

    public function handle(LogItem $item): bool
    {
        if($this->hasOutputDestination())
            return $this->getOutputDestination()->handle($item);
        return false;
    }

    public function hasOutputDestination(): bool
    {
        return ($this->outputDestination !== null);
    }

    public function getOutputDestination(): ?HandlerInterface
    {
        return ($this->outputDestination === null) ? CascadeBuilder::getNullWriter() : $this->outputDestination;
    }

    public function setOutputDestination(?HandlerInterface $handler): ?HandlerInterface
    {
        $oldclass = ($this->outputDestination === null) ? 'NULL (not set/not used yet)' : get_class($this->outputDestination);
        $newclass = get_class($handler);
        $this->outputDestination = $handler;
        $this->outputDestination->handle(LogItem::create(LogLevel::Info, "Logger setOutputDestination:  ".get_called_class()));
        $this->outputDestination->handle(LogItem::create(LogLevel::Info, "   old:  class:   ".$oldclass));
        $this->outputDestination->handle(LogItem::create(LogLevel::Info, "   new:  class:   ".$newclass, ['channel', 'logger']));
        return $handler;
    }
}