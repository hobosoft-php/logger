<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\CascadeBuilder;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Exceptions\HandlerDestinationAlreadySetException;
use Hobosoft\Logger\LogItem;
use Hobosoft\Logger\Writers\NullWriter;
use Monolog\Handler\Handler;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

trait CascadeOutputManyTrait
{
    use CascadeNameTrait;
    use CascadeInputTrait;

    protected array $outputDestinations = [];

    public function isCascadeEnd(): bool
    {
        return (count($this->outputDestinations) === 0);
    }

    public function handle(LogItem $item): bool
    {
        $ret = true;
        foreach ($this->outputDestinations as $outputDestination) {
            if ($outputDestination->handle($item) === false) {
                $ret = false;
            }
        }
        return $ret;
    }

    public function hasOutputDestination(int|string $index): bool
    {
        return isset($this->outputDestinations[$index]) && ($this->outputDestinations[$index] !== null);
    }

    public function getOutputDestination(int|string $index): ?HandlerInterface
    {
        return ($this->hasOutputDestination($index) === false) ? null : $this->outputDestinations[$index];
    }

    public function setOutputDestination(int|string $index, ?HandlerInterface $destination): ?HandlerInterface
    {
        return ($this->outputDestinations[$index] = $destination);
    }

    public function hasOutputDestinations(): bool
    {
        return !in_array(null,$this->outputDestinations, true);
    }

    public function getOutputDestinations(): array
    {
        return $this->outputDestinations;
    }

    public function setOutputDestinations(HandlerInterface|array $destinations, bool $applyToAll = false): ?HandlerInterface
    {
        if($applyToAll) {
            if(is_array($destinations)) {
                if (count($destinations) !== 1) {
                    throw new \Exception("To apply one destination to all outputs, please specify only one destination.");
                }
                $handler = $destinations[0];
            }
            else {
                $handler = $destinations;
            }
            foreach($this->outputDestinations as $k => &$v) {
                $this->outputDestinations[$k] = $handler;
            }
        }
        else {
            foreach($destinations as $index => $destination) {
                if(isset($this->outputDestinations[$index]) === false) {
                    throw new \Exception("When setting multiple destinations, the correct indices in the array must be set.  (index '$index' doesn't exist of ".implode(', ', array_keys($destinations)).")");
                }
                $this->outputDestinations[$index] = $destination;
            }
        }
        return null;
    }
}