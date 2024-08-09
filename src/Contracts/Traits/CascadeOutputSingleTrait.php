<?php

namespace Library\Logger\Contracts\Traits;

use Library\Logger\CascadeBuilder;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Contracts\LogLevel;
use Library\Logger\Exceptions\HandlerDestinationAlreadySetException;
use Library\Logger\LogItem;
use Library\Logger\Writers\NullWriter;
use Monolog\Handler\Handler;

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
        $this->outputDestination->handle(LogItem::create(LogLevel::Info, "Logger setOutputDestination:  ".get_called_class()."\n"));
        $this->outputDestination->handle(LogItem::create(LogLevel::Info, "   old:  class:   ".$oldclass."\n"));
        $this->outputDestination->handle(LogItem::create(LogLevel::Info, "   new:  class:   ".$newclass."\n", ['channel', 'logger']));
        return $handler;
    }
}