<?php

namespace Hobosoft\Logger\Writers;

use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeInputTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\LogItem;

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
