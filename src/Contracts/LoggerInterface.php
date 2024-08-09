<?php

namespace Library\Logger\Contracts;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface
{
    public function createChannel(string $name): ChannelInterface;
    public function getChannelNames(): array;
}