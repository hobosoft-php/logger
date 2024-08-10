<?php

namespace Hobosoft\Logger\Handlers;

use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Hobosoft\Logger\Contracts\Traits\CascadeNameTrait;
use Hobosoft\Logger\LogItem;

class PassthruHandler extends AbstractHandler implements HandlerInterface
{
    use CascadeOutputSingleTrait;

    public function __construct()
    {
    }
}
