<?php

namespace Library\Logger\Processors;

use Library\Logger\Contracts\Handlers\ProcessorInterface;
use Library\Logger\Contracts\Traits\ProcessableHandlerTrait;
use Library\Logger\LogItem;

class TagProcessor extends AbstractProcessor implements ProcessorInterface
{
    use ProcessableHandlerTrait;
    
    public function __construct(
        private array $tags = []
    ) {
        $this->setTags($tags);
    }
    
    public function addTags(array $tags = []): self
    {
        $this->tags = array_merge($this->tags, $tags);
        
        return $this;
    }
    
    public function setTags(array $tags = []): self
    {
        $this->tags = $tags;
        return $this;
    }
    
    public function process(LogItem $item): LogItem
    {
        $item->context['tags'] = $this->tags;
        return $item;
    }
}