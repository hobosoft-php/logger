<?php

namespace Library\Logger\Handlers;

use Library\Logger\Contracts\Handlers\HandlerInterface;
use Library\Logger\Contracts\Traits\CascadeOutputSingleTrait;
use Library\Logger\Contracts\Traits\CascadeNameTrait;
use Library\Logger\LogItem;

class PassthruHandler extends AbstractHandler implements HandlerInterface
{
    use CascadeOutputSingleTrait;

    public function __construct()
    {
    }
}
