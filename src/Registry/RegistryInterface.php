<?php

namespace Hobosoft\Logger\Registry;

interface RegistryInterface
{
    public function set(string $name, mixed $callback): self;
    public function get(string $name): mixed;
    public function has(string $name): bool;
}