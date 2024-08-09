<?php

namespace Library\Logger\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class WithMonologChannel
{
    public function __construct(
        public readonly string $channel
    ) {
    }
}