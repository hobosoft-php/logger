<?php

namespace Library\Logger\Writers;

use Library\Logger\Contracts\Handlers\WriterInterface;
use Library\Logger\Contracts\Traits\CascadeInputTrait;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\LogItem;

class NullWriter extends AbstractWriter implements WriterInterface
{
    //use CascadeInputTrait;
    use CascadeOutputSingleTrait {
        CascadeOutputSingleTrait::__construct as __traitConstruct;
    }

    public function handle(LogItem $item): bool
    {
        return true;
    }
    
    public function close(): void
    {
    }
}
