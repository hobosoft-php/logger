<?php

namespace Hobosoft\Logger\Contracts\HandlerOptions;

use Closure;
use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;

/**
 * Formatters should only go on the tail end of a chain of handlers.
 */
interface FormattableInterface
{
    /**
     * Sets the formatter.
     */
    public function setFormatter(FormatterInterface|Closure $formatter): self;

    /**
     * Gets the formatter.
     */
    public function getFormatter(): FormatterInterface|Closure;
}
