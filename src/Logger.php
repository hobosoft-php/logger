<?php

namespace Hobosoft\Logger;

use Closure;
use Hobosoft\Config\Contracts\ConfigAwareInterface;
use Hobosoft\Config\Contracts\ConfigAwareTrait;
use Hobosoft\Logger\Contracts\Traits\CloseAwareTrait;
use Hobosoft\Logger\Contracts\Traits\FlushAwareTrait;
use Hobosoft\Logger\Contracts\Traits\ResetAwareTrait;
use Hobosoft\Config\Contracts\ConfigInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ClosableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\FlushableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\LoggerInterface;
use Hobosoft\Logger\Contracts\LogLevel;
use Hobosoft\Logger\Contracts\Traits\ChannelsTrait;
use Hobosoft\Logger\Exceptions\InvalidArgumentException;
use Hobosoft\Logger\Formatters\LineFormatter;
use Hobosoft\Logger\Handlers\ProcessorHandler;
use Hobosoft\Logger\Processors\TimestampProcessor;
use Hobosoft\Logger\Writers\PrintWriter;
use Psr\Log\AbstractLogger as PsrAbstractLogger;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class Logger extends PsrAbstractLogger implements LoggerInterface, ResettableInterface, FlushableInterface, ClosableInterface, ConfigAwareInterface
{
	protected bool $showTimestamps;

    use ChannelsTrait, ResetAwareTrait, FlushAwareTrait, CloseAwareTrait;
    use ConfigAwareTrait {
        ConfigAwareTrait::setConfig as internalSetConfig;
    }

	public function __construct(
		protected HandlerInterface|Closure|null $writer = null,
	) {
		$showTs = $this->showTimestamps = false; //$config->get('logger.config.show_timestamps', false);
		//$processor = new ProcessorHandler('logger_processor', $showTs ? [new TimestampProcessor()] : []);
		$processors = $showTs ? [new TimestampProcessor()] : [];
		if ($this->writer instanceof Closure) {
			$this->writer = ($this->writer)($this->asPsrLogger());
		}
		if (is_null($this->writer)) {
            $this->writer = new ProcessorHandler('logger_processor', $processors);
            $this->writer->setOutputDestination(new PrintWriter())->setFormatter(new LineFormatter());
        }

		if ($this->writer instanceof CascadeBuilder) {
			$this->writer = $this->writer->imitate(new ProcessorHandler('logger_processor', $processors));
		} elseif ($this->writer instanceof ProcessorHandler) {
			$this->writer->pushProcessor($processors);
		} else {
			die("The passed HandlerInterface to the Logger constructor should be a ProcessorHandler or CascadeBuilder.");
		}
	}

    public function setConfig(ConfigInterface $config): void
    {
        $this->internalSetConfig($config);
        //process the configuration and update stuff
    }

    public function __destruct() {
		$debugPath = ROOTPATH . '/var/debug-' . PHP_SAPI . '/logger-dump.txt';
		$ret = '';
		foreach ($this->channels as $channel) {
			$ret .= print_r($channel, true);
		}
		file_put_contents($debugPath, $ret);
		$this->flush();
		$this->close();
	}

    public function getWriter(): ?HandlerInterface
    {
        return $this->writer;
    }

    public function asPsrLogger(): PsrLoggerInterface
    {
        return $this;
    }

    public function log($level, $message = null, array $context = []): void {
		if ($level instanceof LogItem) {
			$item = $level;
		} elseif (!$level instanceof LogLevel && !is_string($level) && !is_int($level)) {
			throw new InvalidArgumentException('$level is expected to be a string, int or ' . LogLevel::class . ' instance');
		} else {
            $item = LogItem::create(LogLevel::normalize($level), $message, $context);
		}
        $this->writer->handle(clone $item);
	}
}
