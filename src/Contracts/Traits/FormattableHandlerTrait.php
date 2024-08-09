<?php

namespace Library\Logger\Contracts\Traits;

use Closure;
use Library\Logger\Contracts\Handlers\FormatterInterface;
use Library\Logger\Formatters\LineFormatter;
use Library\Logger\Formatters\PassthruFormatter;
use Library\Logger\LogItem;

trait FormattableHandlerTrait
{
    protected FormatterInterface|Closure|null $formatter = null;
    
    const string DEFAULT_FORMATTER = '\\Library\\Logger\\Formatters\\PassthruFormatter::class';
    
    protected function formatRecord(LogItem $item): mixed
    {
        return $this->getFormatter()->format($item);
    }
    
    /**
     * @inheritDoc
     */
    public function setFormatter(FormatterInterface|Closure $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): FormatterInterface|Closure
    {
        if (null === $this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }
        if ($this->formatter instanceof \Closure) {
            $this->formatter = ($this->formatter)();
        }
        return $this->formatter;
    }

    /**
     * Gets the default formatter.
     *
     * Overwrite this if the LineFormatter is not a good default for your handler.
     */
    protected function getDefaultFormatter(): FormatterInterface|Closure
    {
        return new PassthruFormatter();
        //return new (self::DEFAULT_FORMATTER)();
    }
}