<?php

namespace Hobosoft\Logger\Registry;

use ArrayAccess;

class Registry implements RegistryInterface, ArrayAccess
{
    protected array $items = [];

    public function set(string $name, mixed $callback): self
    {
        $this->items[$name] = $callback;
        return $this;
    }

    public function get(string $name): mixed
    {
        return ($this->has($name)) ? $this->items[$name] : null;
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->items);
    }
    
    public function unset(string $name): self
    {
        unset($this->items[$name]);
        return $this;
    }
    
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }
    
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }
    
    public function offsetUnset(mixed $offset): void
    {
        $this->unset($offset);
    }
}