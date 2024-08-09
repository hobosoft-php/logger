<?php

namespace Library\Logger;

use Closure;
use Library\Logger\Contracts\ChannelInterface;
use Library\Logger\Contracts\HandlerOptions\ProcessableInterface;
use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\Contracts\LogLevel;
use Library\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Library\Logger\Processors\ChannelProcessor;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Channel extends AbstractLogger implements ChannelInterface, ProcessableInterface
{
    public $writer;
    use LoggerTrait;
    use ProcessableHandlerTrait;
    
    private bool $isClosed = false;

    public function __construct(
        protected LoggerInterface $logger,
        protected string $name,
    ) {
        $this->pushProcessor(new ChannelProcessor($name));
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if($this->isClosed) {
            $this->logger->log(LogLevel::Notice, "Channel:  log channel '{$this->name}' is closed.");
            return;
        }
        $this->logger->log($this->processRecord(LogItem::create(LogLevel::normalize($level), $message, $context)), '');
    }
    
    public static function create(LoggerInterface $logger, string $name): Channel
    {
        return $logger->createChannel($name);
    }
    
    public function close(): void
    {
        $this->logger->closeChannel($this->name);
        $this->isClosed = true;
    }
    
    public function getWriter(): ?HandlerInterface
    {
        return $this->writer;
    }
}