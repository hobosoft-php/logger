<?php

namespace Hobosoft\Logger\Contracts\HandlerOptions;

use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\LogItem;

interface CascadableOutputManyInterface extends HandlerInterface
{
    public function isCascadeStart(): bool;
    public function isCascadeEnd(): bool;
    public function handle(LogItem $item): bool;
    public function handleBatch(array $items): void;
    public function hasOutputDestination(int|string $index): bool;
    public function getOutputDestination(int|string $index): ?HandlerInterface;
    public function setOutputDestination(int|string $index, ?HandlerInterface $destination): ?HandlerInterface;
    public function hasOutputDestinations(): bool;
    public function getOutputDestinations(): array;
    public function setOutputDestinations(HandlerInterface|array $destinations, bool $applyToAll = false): ?HandlerInterface;
}