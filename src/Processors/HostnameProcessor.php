<?php

namespace Hobosoft\Logger\Processors;

use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\LogItem;

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
