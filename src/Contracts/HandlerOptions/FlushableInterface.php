<?php

namespace Library\Logger\Contracts\HandlerOptions;

interface FlushableInterface
{
    public function flush(): void;
}
