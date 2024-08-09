<?php

namespace Library\Logger\Contracts\Traits;

use Library\Logger\CascadeBuilder;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Exceptions\HandlerDestinationAlreadySetException;
use Library\Logger\LogItem;
use Library\Logger\Writers\NullWriter;
use Monolog\Handler\Handler;

trait CascadeInputTrait
{
    protected array $inputSources = [];

    public function isCascadeStart(): bool
    {
        return (count($this->inputSources) === 0);
    }

    public function hasInputSource(int|string $index): bool
    {
        return isset($this->inputSource[$index]) && ($this->inputSource[$index] !== null);
    }

    public function getInputSource(int|string $index = 0): ?HandlerInterface
    {
        return ($this->hasInputSource($index) === false) ? null : $this->inputSources[$index];
    }
    
    public function setInputSource(?HandlerInterface $source, bool $errorIfSet = true): void
    {
        if ($this->hasInputSource($source->getName()) === false) {
            $this->inputSources[$source->getName()] = $source;
        } elseif ($errorIfSet) {
            throw new HandlerDestinationAlreadySetException("Input source already set for '".$source->getName()."'.");
        }
    }
}