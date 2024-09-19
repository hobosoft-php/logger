<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;

trait ResetAwareTrait
{
    public function reset(): void {
        if ($this->writer instanceof ResettableInterface) {
            $this->writer->reset();
        }
        $this->resetChannels();
    }
}