<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\Contracts\HandlerOptions\ClosableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;

trait CloseAwareTrait
{
    public function close(): void {
        if ($this->writer instanceof ClosableInterface) {
            $this->writer->close();
        }
        $this->closeChannels();
    }
}