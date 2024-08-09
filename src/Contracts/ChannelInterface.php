<?php

namespace Library\Logger\Contracts;

use Library\Logger\Contracts\HandlerOptions\ClosableInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface ChannelInterface extends PsrLoggerInterface, ClosableInterface
{
}
