<?php

namespace Library\Logger\Processors;

use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\LogItem;

class HostnameProcessor extends AbstractProcessor implements ProcessorInterface
{
    protected ?string $hostname = null;

    public function process(LogItem $item): LogItem
    {
        if(is_null($this->hostname)) {
            $this->hostname = gethostname();
        }
        $item->context['hostname'] = $this->hostname;
        return $item;
    }
}
