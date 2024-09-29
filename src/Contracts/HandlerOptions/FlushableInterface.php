<?php

namespace Hobosoft\Logger\Contracts\HandlerOptions;

interface FlushableInterface
{
    public function flush(): void;
}
