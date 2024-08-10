<?php

namespace Hobosoft\Logger\Filters;

use Hobosoft\Logger\Contracts\HandlerOptions\AcceptableInterface;
use Hobosoft\Logger\Contracts\Handlers\FilterInterface;
use Hobosoft\Logger\Contracts\LogLevel;
use Hobosoft\Logger\LogItem;

class TagFilter implements FilterInterface
{
    const int MODE_INCLUDE_TAGS = 0;
    const int MODE_EXCLUDE_TAGS = 1;
    
    public function __construct(
        protected string|array $tags,
        protected int $mode = self::MODE_INCLUDE_TAGS,
    )
    {
        $this->tags = is_string($tags) ? [$tags] : $tags;
    }
    
    public function accept(LogItem $item): bool
    {
        $itemTags = $item->context['tags'];
        if($this->mode === self::MODE_INCLUDE_TAGS) {
            foreach($this->tags as $tag) {
                if (in_array($tag, $itemTags)) {
                    return true;
                }
            }
        }
        else {
            foreach($this->tags as $tag) {
                if (!in_array($tag, $itemTags)) {
                    return true;
                }
            }
        }
        return false;
    }
}