<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\CascadeBuilder;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Exceptions\HandlerDestinationAlreadySetException;
use Hobosoft\Logger\LogItem;
use Hobosoft\Logger\Writers\NullWriter;
use Monolog\Handler\Handler;

trait CascadeNameTrait
{
    protected ?string $name = null;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}