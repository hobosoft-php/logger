<?php

namespace Hobosoft\Logger;

use Closure;
use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ClosableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\FlushableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\LoggerInterface;
use Hobosoft\Logger\Contracts\LogLevel;
use Hobosoft\Logger\Contracts\Traits\ChannelsTrait;
use Hobosoft\Logger\Handlers\ProcessorHandler;
use Hobosoft\Logger\Processors\TimestampProcessor;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger implements LoggerInterface, ResettableInterface, FlushableInterface, ClosableInterface
{
    protected bool $showTimestamps;

    use ChannelsTrait;

    public function __construct(
        protected ConfigInterface $config,
        protected HandlerInterface|Closure|null $writer = null,
    )
    {
        $showTs = $this->showTimestamps = $config->get('logger.config.show_timestamps', false);
        //$processor = new ProcessorHandler('logger_processor', $showTs ? [new TimestampProcessor()] : []);
        $processors = $showTs ? [new TimestampProcessor()] : [];
        if($this->writer instanceof Closure) {
            $this->writer = ($this->writer)($this);
        }
        if(is_null($this->writer)) {
            $this->writer = new ProcessorHandler('logger_processor', $processors);
        }

        if ($this->writer instanceof CascadeBuilder) {
            $this->writer = $this->writer->imitate(new ProcessorHandler('logger_processor', $processors));
        } elseif ($this->writer instanceof ProcessorHandler) {
            $this->writer->pushProcessor($processors);
        } else {
            die("The passed HandlerInterface to the Logger constructor should be a ProcessorHandler or CascadeBuilder.");
        }
    }

    public function __destruct()
    {
        $this->flush();
        $this->close();
    }

    public function getWriter(): ?HandlerInterface
    {
        return $this->writer;
    }

    public function log(mixed $level, \Stringable|string $message, array $context = []): void
    {
        if ($level instanceof LogItem) {
            $item = $level;
        } elseif (!$level instanceof LogLevel && !is_string($level) && !is_int($level)) {
            throw new \InvalidArgumentException('$level is expected to be a string, int or '.LogLevel::class.' instance');
        } else {
            $item = LogItem::create(LogLevel::normalize($level), $message, $context);
        }
        $this->writer->handle(clone $item);
    }

    public function reset(): void
    {
        if ($this->writer instanceof ResettableInterface) {
            $this->writer->reset();
        }
        $this->resetChannels();
    }

    public function flush(): void
    {
        if ($this->writer instanceof FlushableInterface) {
            $this->writer->flush();
        }
        $this->flushChannels();
    }
    
    public function close(): void
    {
        if ($this->writer instanceof ClosableInterface) {
            $this->writer->close();
        }
        $this->closeChannels();
    }
}
