<?php

namespace Hobosoft\Logger;

use Closure;
use Hobosoft\Logger\Contracts\ChannelInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ProcessableInterface;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\Contracts\LogLevel;
use Hobosoft\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Hobosoft\Logger\Processors\ChannelProcessor;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Channel extends AbstractLogger implements ChannelInterface, ProcessableInterface
{
    public ?HandlerInterface $writer;
    use LoggerTrait;
    use ProcessableHandlerTrait;
    
    private bool $isClosed = false;

    public function __construct(
        protected LoggerInterface $logger,
        protected string $name,
    ) {
        $this->pushProcessor(new ChannelProcessor($name));
    }

    public function log($level, $message, array $context = []): void
    {
        if($this->isClosed) {
            $this->logger->log(LogLevel::Notice, "Channel:  log channel '{$this->name}' is closed.");
            return;
        }
        $this->logger->log($this->processRecord(LogItem::create(LogLevel::normalize($level), $message, $context + ['channel' => $this->name])));
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