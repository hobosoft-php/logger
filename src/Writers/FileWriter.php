<?php

namespace Library\Logger\Writers;

use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\Contracts\Traits\FormattableHandlerTrait;
use Library\Logger\LogItem;

class FileWriter extends AbstractWriter implements WriterInterface
{
    use FormattableHandlerTrait;
    use CascadeOutputSingleTrait {
        CascadeOutputSingleTrait::__construct as __traitConstruct;
    }

    public function __construct(
        string $name,
        protected string $filename,
    )
    {
        $this->__traitConstruct($name);
    }

    public function handle(LogItem $item): bool
    {
        file_put_contents($this->filename, $str, FILE_APPEND);
        return true;
    }

    public function close(): void
    {

    }

    public function flush(): void
    {
        // TODO: Implement flush() method.
    }
}
