<?php

namespace Library\Logger\Contracts\Traits;

use Library\Logger\CascadeBuilder;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Exceptions\HandlerDestinationAlreadySetException;
use Library\Logger\LogItem;
use Library\Logger\Writers\NullWriter;
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