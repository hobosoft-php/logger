<?php

namespace Library\Logger;

use ArrayAccess;
use Library\Logger\Contracts\LogLevel;

class LogItem implements ArrayAccess
{
    public ?string $formatted = null;

    public function __construct(
        public readonly LogLevel $level,
        public readonly string $message,
        public array $context = [],
    ) {
    }
    
    public static function create(LogLevel $level, string $message, array $context = []): self
    {
        return new self($level, $message, $context);
    }

    public function toArray(): array
    {
        return [
            'level' => $this->level->value,
            'level_name' => $this->level->getName(),
            'message' => $this->message,
            'context' => $this->context,
        ];
    }

    public function __toString(): string
    {
        if($this->formatted === null) {
            $this->formatted = $this->level->getName() . ':  ' . $this->message;
        }
        return $this->formatted;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }
}