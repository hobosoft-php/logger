<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\CascadeBuilder;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Exceptions\HandlerDestinationAlreadySetException;
use Hobosoft\Logger\LogItem;
use Hobosoft\Logger\Writers\NullWriter;
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