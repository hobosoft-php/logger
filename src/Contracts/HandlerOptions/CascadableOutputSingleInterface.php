<?php

namespace Library\Logger\Contracts\HandlerOptions;

use Library\Logger\CascadeBuilder;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\LogItem;

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