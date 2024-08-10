<?php

namespace Hobosoft\Logger\Contracts\HandlerOptions;

use Hobosoft\Logger\CascadeBuilder;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\LogItem;

interface CascadableOutputSingleInterface extends HandlerInterface
{
    public function isCascadeStart(): bool;
    public function isCascadeEnd(): bool;
    public function handle(LogItem $item): bool;
    public function handleBatch(array $items): void;
    public function hasOutputDestination(): bool;
    public function getOutputDestination(): ?HandlerInterface;
    public function setOutputDestination(?HandlerInterface $handler): ?HandlerInterface;
}