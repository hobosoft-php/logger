<?php

namespace Library\Logger\Contracts\Traits;

use Library\Logger\Channel;
use Library\Logger\Contracts\ChannelInterface;
use Library\Logger\Contracts\HandlerOptions\ClosableInterface;
use Library\Logger\Contracts\HandlerOptions\FlushableInterface;
use Library\Logger\Contracts\HandlerOptions\ResettableInterface;
use Library\Logger\Exceptions\ChannelAlreadyCreatedException;
use Library\Logger\LogItem;

trait ChannelsTrait
{
    protected array $channels = [];
    
    public function createChannel(string $name): ChannelInterface
    {
        if (isset($this->channels[$name])) {
            throw new ChannelAlreadyCreatedException("Logger channel '$name' has already been created.");
        }
        return ($this->channels[$name] = new Channel($this, $name));
    }
    
    public function closeChannel(string $name): void
    {
        if(isset($this->channels[$name]) === false) {
            return;
        }
        unset($this->channels[$name]);
    }
    
    public function getChannelNames(): array
    {
        return array_keys($this->channels);
    }
    
    protected function resetChannels(): void
    {
        foreach ($this->channels as $channel) {
            if ($channel instanceof ResettableInterface) {
                $channel->reset();
            }
        }
    }
    
    protected function flushChannels(): void
    {
        foreach ($this->channels as $channel) {
            if ($channel instanceof FlushableInterface) {
                $channel->flush();
            }
        }
    }
    
    protected function closeChannels(): void
    {
        foreach ($this->channels as $channel) {
            if ($channel instanceof ClosableInterface) {
                $channel->close();
            }
        }
    }
}