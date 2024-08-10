<?php

namespace Hobosoft\Logger\Contracts;

use Hobosoft\Logger\Contracts\HandlerOptions\ClosableInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface ChannelInterface extends PsrLoggerInterface, ClosableInterface
{
}
