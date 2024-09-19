<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\LogItem;

class StdoutWriter extends StreamWriter implements WriterInterface
{
    public function __construct()
    {
        parent::__construct('php://stdout');
    }

    public function handle(LogItem $record): bool
    {
        parent::handle($record);
        return true;
    }

    public function flush(): void
    {
        fflush($this->stream);
    }
}