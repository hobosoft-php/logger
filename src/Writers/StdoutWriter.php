<?php

namespace Library\Logger\Writers;

use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\LogItem;
use Plugins\LogExtra\Writers\StreamWriter;

return;

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