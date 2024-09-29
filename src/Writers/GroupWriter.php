<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\HandlerOptions\ResettableInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Hobosoft\Logger\LogItem;

class GroupWriter extends AbstractWriter implements WriterInterface
{
    use ProcessableHandlerTrait;

    public function __construct(
        protected array $writers = []
    ) {
    }

    public function handle(LogItem $item): bool
    {
        $ret = false;
        foreach($this->writers as $writer) {
            if ($writer->handle($item) === true) {
                $ret = true;
            }
        }
        return $ret;
    }

    public function close(): void
    {
        foreach ($this->writers as $writer) {
            $writer->close();
        }
    }

    public function reset(): void
    {
        foreach ($this->writers as $writer) {
            if ($writer instanceof ResettableInterface) {
                $writer->reset();
            }
        }
    }

    public function flush(): void
    {
        foreach ($this->writers as $writer) {
            $writer->flush();
        }
    }
}