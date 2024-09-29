<?php

namespace Hobosoft\Logger\Contracts\Traits;

use Hobosoft\Logger\Contracts\HandlerOptions\FlushableInterface;
use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;

trait FlushAwareTrait
{
    public function flush(): void {
        if ($this->writer instanceof FlushableInterface) {
            $this->writer->flush();
        }
        $this->flushChannels();
    }
}