<?php

namespace Hobosoft\Logger;

use BadMethodCallException;
use Library\Config\Definitions\Exceptions\Exception;
use Hobosoft\Logger\Contracts\LoggerInterface;
use Hobosoft\Logger\Contracts\Handlers\FilterInterface;
use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;
use Hobosoft\Logger\Contracts\Handlers\HandlerInterface;
use Hobosoft\Logger\Contracts\Handlers\ProcessorInterface;
use Hobosoft\Logger\Contracts\Handlers\WriterInterface;
use Hobosoft\Logger\Contracts\Traits\FormattableHandlerTrait;
use Hobosoft\Logger\Formatters\LineFormatter;
use Hobosoft\Logger\Handlers\AbstractHandler;
use Hobosoft\Logger\Writers\NullWriter;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class CascadeBuilder extends AbstractHandler implements HandlerInterface
{
    //use CascadeOutputSingleTrait {
    //    CascadeOutputSingleTrait::__construct as __traitConstruct;
    //}
    use FormattableHandlerTrait;

    const string IMITATE_HANDLER_NAME = 'imitation_target';

    private static ?NullWriter $nullWriter = null;
    private ?HandlerInterface $imitateHandler = null;
    protected array $handlers = [];

    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function handle(LogItem $item): bool
    {
        if ($this->imitateHandler instanceof \Hobosoft\Logger\Contracts\Handlers\HandlerInterface) {
            return $this->imitateHandler->handle($item);
        }
        return false;
    }

    public static function getNullWriter()
    {
        if (is_null(self::$nullWriter)) {
            self::$nullWriter = new NullWriter();
        }
        return self::$nullWriter;
    }

    public function traceHandlers(): void
    {
        $maxDepth = 10;
        print("\n");
        $handler = $this;
        for($i=0; $handler !== null; $i++) {
            print("Depth $i:  ".get_class($handler)."\n");
            $handler = $handler->getOutputDestination();
            if($handler === ($newHandler = $handler->getOutputDestination())) {
                throw new Exception("traceHandlers current handler points to itself!");
            }
            $handler = $newHandler;
            if($i >= $maxDepth) {
                throw new Exception("traceHandlers going wild!");
            }
        }
    }

    public function __call(string $name, array $arguments): mixed
    {
        if($this->imitateHandler instanceof \Hobosoft\Logger\Contracts\Handlers\HandlerInterface) {
            if (method_exists($this->imitateHandler, $name)) {
                return $this->imitateHandler->$name(...$arguments);
            }
            debug_print_backtrace();
            throw new BadMethodCallException("Method {$name} does not exist in class '".get_class($this->imitateHandler)."'.");
        }
        debug_print_backtrace();
        throw new BadMethodCallException("Cannot call method {$name}, imitation has not been enabled yet.");
    }

    public function imitate(HandlerInterface|string $handler): self
    {
        $this->imitateHandler = (is_string($handler)) ? new $handler(self::IMITATE_HANDLER_NAME) : $handler;
        $this->setFormatter(new LineFormatter());
        return $this;
    }

    /**
     * @return HandlerInterface|WriterInterface|FormatterInterface|ProcessorInterface|FilterInterface
     */
    public function add(string $name, mixed $obj): mixed
    {
        $obj = (is_string($obj)) ? new $obj($name) : $obj;
        $type = match(true) {
            $obj instanceof ProcessorInterface => 'processor',
            $obj instanceof FormatterInterface => 'formatter',
            $obj instanceof FilterInterface => 'filter',
            $obj instanceof WriterInterface => 'writer',
            $obj instanceof HandlerInterface => 'handler',
            default => die("Invalid class type passed to ".__METHOD__."\n"), //'unknown'
        };
        $this->handlers[$name] = [
            'name' => $name,
            'type' => $type,
            'instance' => $obj,
        ];
        return $obj;
    }

    public function get(string $name): mixed
    {
        if(isset($this->handlers[$name])) {
            return $this->handlers[$name]['instance'];
        }
        return null;
    }

    /*
        public function addCascadeHandler(Writers\BufferWriter $param, string $destHandlerName = null, array|string $destPath = []): void
        {
            $writer = $this;
            while($writer->hasOutputDestination()) {
                $handler = $writer->getOutputDestination();
                $outputs = $handler->getOutputs();
                $writer = $handler;
            }
        }*/
    public function getCascadeEnd(): ?HandlerInterface
    {
        /** @var HandlerInterface $handler */
        $handler = $this->imitateHandler;
        while($handler !== null) {

            print("handler is ".get_class($handler)."\n");
            if($handler->hasOutputDestination() === false) {
                print("handler has no outputs!\n");
                return $handler;
            }
            else {
            }

            $handler = $handler->getOutputDestination();
        }
        return null;
    }

    public function dump(): void
    {
        /** @var HandlerInterface $handler */
        $handler = $this->imitateHandler;
        while($handler !== null) {

            $name = $handler->getName();
            if($handler->hasOutputDestination() === false) {
                print("handler (name: '$name') is ".get_class($handler)." which outputs to nowhere\n");
                print("handler has no outputs!\n");
                break;
            }
            else {
                $desthandler = $handler->getOutputDestination();
                $destname = $handler->getName();
                print("handler (name: '$name') is ".get_class($handler)." which outputs to handler (name: '$destname') is ".get_class($desthandler)."\n");
            }

            $handler = $handler->getOutputDestination();
        }
    }

}
