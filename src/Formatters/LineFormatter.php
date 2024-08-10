<?php

namespace Hobosoft\Logger\Formatters;

use DateTimeImmutable;
use Hobosoft\Logger\Contracts\Handlers\FormatterInterface;
use Hobosoft\Logger\LogItem;

class LineFormatter extends AbstractFormatter implements FormatterInterface
{
    public const SIMPLE_FORMAT = "[%timestamp%] %channel%.%level_name%: %message% %context%\n";

    public function __construct(
        private int $maxLevelNameLength = 10,
        private bool $allowInlineLineBreaks = true,
        private bool $ignoreEmptyContextAndExtra = true,
        private ?string $format = null
    )
    {
        $this->format = ($format === null) ? static::SIMPLE_FORMAT : $format;
    }

    public function format(LogItem $item): mixed
    {
        $vars = $this->normalizeRecord($item);
        $vars['channel'] = $vars['context']['channel'] ?? 'default';
        $vars['timestamp'] = $vars['context']['timestamp'] ?? 0;
        unset($vars['context']['channel']);
        unset($vars['context']['timestamp']);

        if ($this->maxLevelNameLength !== null) {
            $vars['level_name'] = substr($vars['level_name'], 0, $this->maxLevelNameLength);
        }

        $output = $this->format;
        /*foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }*/

        foreach ($vars['context'] as $var => $val) {
            if (false !== strpos($output, '%context.'.$var.'%')) {
                $output = str_replace('%context.'.$var.'%', $this->stringify($val), $output);
                unset($vars['context'][$var]);
            }
        }

        if ($this->ignoreEmptyContextAndExtra) {
            if (\count($vars['context']) === 0) {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            /*if (\count($vars['extra']) === 0) {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }*/
        }

        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->stringify($val), $output);
            }
        }

        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
            if (null === $output) {
                $pcreErrorCode = preg_last_error();

                throw new \RuntimeException('Failed to run preg_replace: ' . $pcreErrorCode . ' / ' . Utils::pcreLastErrorMessage($pcreErrorCode));
            }
        }
        $item->formatted = $output;
        return $item;
        //return $output;
    }

    public function stringify($value): string
    {
        return $this->replaceNewlines($this->convertToString($value));
    }

    protected function convertToString($data): string
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string) $data;
        }

        return $this->toJson($data, true);
    }

    protected function replaceNewlines(string $str): string
    {
        if ($this->allowInlineLineBreaks) {
            if (0 === strpos($str, '{') || 0 === strpos($str, '[')) {
                $str = preg_replace('/(?<!\\\\)\\\\[rn]/', "\n", $str);
                if (null === $str) {
                    $pcreErrorCode = preg_last_error();
                    throw new \RuntimeException('Failed to run preg_replace: ' . $pcreErrorCode . ' / ' . Utils::pcreLastErrorMessage($pcreErrorCode));
                }
            }

            return $str;
        }

        return str_replace(["\r\n", "\r", "\n"], ' ', $str);
    }

    protected function normalizeRecord(LogItem $record): array
    {
        /** @var array<mixed> $normalized */
        $normalized = $this->normalize($record->toArray());

        return $normalized;
    }

    private int $maxNormalizeDepth = 5;
    private int $maxNormalizeItemCount = 50;
    
    protected function normalize(mixed $data, int $depth = 0): mixed
    {
        if ($depth > $this->maxNormalizeDepth) {
            return 'Over ' . $this->maxNormalizeDepth . ' levels deep, aborting normalization';
        }

        if (null === $data || is_scalar($data)) {
            if (is_float($data)) {
                if (is_infinite($data)) {
                    return ($data > 0 ? '' : '-') . 'INF';
                }
                if (is_nan($data)) {
                    return 'NaN';
                }
            }

            return $data;
        }

        if (is_array($data)) {
            $normalized = [];

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ > $this->maxNormalizeItemCount) {
                    $normalized['...'] = 'Over ' . $this->maxNormalizeItemCount . ' items ('.count($data).' total), aborting normalization';
                    break;
                }

                $normalized[$key] = $this->normalize($value, $depth + 1);
            }

            return $normalized;
        }

        if ($data instanceof \DateTimeInterface) {
            return $this->formatDate($data);
        }

        if (is_object($data)) {
            if ($data instanceof \Throwable) {
                return $this->normalizeException($data, $depth);
            }

            if ($data instanceof \JsonSerializable) {
                /** @var null|scalar|array<mixed[]|scalar|null> $value */
                $value = $data->jsonSerialize();
            } elseif (\get_class($data) === '__PHP_Incomplete_Class') {
                $accessor = new \ArrayObject($data);
                $value = (string) $accessor['__PHP_Incomplete_Class_Name'];
            } elseif (method_exists($data, '__toString')) {
                try {
                    /** @var string $value */
                    $value = $data->__toString();
                } catch (\Throwable) {
                    // if the toString method is failing, use the default behavior
                    /** @var null|scalar|array<mixed[]|scalar|null> $value */
                    $value = json_decode($this->toJson($data, true), true);
                }
            } else {
                // the rest is normalized by json encoding and decoding it
                /** @var null|scalar|array<mixed[]|scalar|null> $value */
                $value = json_decode($this->toJson($data, true), true);
            }

            return [self::getClass($data) => $value];
        }

        if (is_resource($data)) {
            return sprintf('[resource(%s)]', get_resource_type($data));
        }

        return '[unknown('.gettype($data).')]';
    }

    protected function normalizeException(\Throwable $e, int $depth = 0)
    {
        if ($depth > $this->maxNormalizeDepth) {
            return ['Over ' . $this->maxNormalizeDepth . ' levels deep, aborting normalization'];
        }

        if ($e instanceof \JsonSerializable) {
            return (array) $e->jsonSerialize();
        }

        $data = [
            'class' => self::getClass($e),
            'message' => $e->getMessage(),
            'code' => (int) $e->getCode(),
            'file' => $e->getFile().':'.$e->getLine(),
        ];

        if ($e instanceof \SoapFault) {
            if (isset($e->faultcode)) {
                $data['faultcode'] = $e->faultcode;
            }

            if (isset($e->faultactor)) {
                $data['faultactor'] = $e->faultactor;
            }

            if (property_exists($e, 'detail') && $e->detail !== null) {
                if (is_string($e->detail)) {
                    $data['detail'] = $e->detail;
                } elseif (is_object($e->detail) || is_array($e->detail)) {
                    $data['detail'] = $this->toJson($e->detail, true);
                }
            }
        }

        $trace = $e->getTrace();
        foreach ($trace as $frame) {
            if (isset($frame['file'], $frame['line'])) {
                $data['trace'][] = $frame['file'].':'.$frame['line'];
            }
        }

        if (($previous = $e->getPrevious()) instanceof \Throwable) {
            $data['previous'] = $this->normalizeException($previous, $depth + 1);
        }

        return $data;
    }

    public static function getClass(object $object): string
    {
        $class = \get_class($object);

        if (false === ($pos = \strpos($class, "@anonymous\0"))) {
            return $class;
        }

        if (false === ($parent = \get_parent_class($class))) {
            return \substr($class, 0, $pos + 10);
        }

        return $parent . '@anonymous';
    }

    protected function toJson($data, bool $ignoreErrors = false): string
    {
        return self::jsonEncode($data, $this->jsonEncodeOptions, $ignoreErrors);
    }

    const DEFAULT_JSON_FLAGS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR;
    private int $jsonEncodeOptions = self::DEFAULT_JSON_FLAGS;

    public static function jsonEncode($data, ?int $encodeFlags = null, bool $ignoreErrors = false): string
    {
        if (null === $encodeFlags) {
            $encodeFlags = self::DEFAULT_JSON_FLAGS;
        }

        if ($ignoreErrors) {
            $json = @json_encode($data, $encodeFlags);
            if (false === $json) {
                return 'null';
            }

            return $json;
        }

        $json = json_encode($data, $encodeFlags);
        if (false === $json) {
            $json = self::handleJsonError(json_last_error(), $data);
        }

        return $json;
    }

    public static function handleJsonError(int $code, $data, ?int $encodeFlags = null): string
    {
        if ($code !== JSON_ERROR_UTF8) {
            self::throwEncodeError($code, $data);
        }

        if (is_string($data)) {
            self::detectAndCleanUtf8($data);
        } elseif (is_array($data)) {
            array_walk_recursive($data, ['Monolog\Utils', 'detectAndCleanUtf8']);
        } else {
            self::throwEncodeError($code, $data);
        }

        if (null === $encodeFlags) {
            $encodeFlags = self::DEFAULT_JSON_FLAGS;
        }

        $json = json_encode($data, $encodeFlags);

        if ($json === false) {
            self::throwEncodeError(json_last_error(), $data);
        }

        return $json;
    }

    private static function throwEncodeError(int $code, $data): never
    {
        $msg = match ($code) {
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
            default => 'Unknown error',
        };

        throw new \RuntimeException('JSON encoding failed: '.$msg.'. Encoding: '.var_export($data, true));
    }

    /**
     * Detect invalid UTF-8 string characters and convert to valid UTF-8.
     *
     * Valid UTF-8 input will be left unmodified, but strings containing
     * invalid UTF-8 codepoints will be reencoded as UTF-8 with an assumed
     * original encoding of ISO-8859-15. This conversion may result in
     * incorrect output if the actual encoding was not ISO-8859-15, but it
     * will be clean UTF-8 output and will not rely on expensive and fragile
     * detection algorithms.
     *
     * Function converts the input in place in the passed variable so that it
     * can be used as a callback for array_walk_recursive.
     *
     * @param mixed $data Input to check and convert if needed, passed by ref
     */
    private static function detectAndCleanUtf8(&$data): void
    {
        if (is_string($data) && preg_match('//u', $data) !== 1) {
            $data = preg_replace_callback(
                '/[\x80-\xFF]+/',
                function (array $m): string {
                    return function_exists('mb_convert_encoding') ? mb_convert_encoding($m[0], 'UTF-8', 'ISO-8859-1') : utf8_encode($m[0]);
                },
                $data
            );
            if (!is_string($data)) {
                $pcreErrorCode = preg_last_error();

                throw new \RuntimeException('Failed to preg_replace_callback: ' . $pcreErrorCode . ' / ' . self::pcreLastErrorMessage($pcreErrorCode));
            }
            $data = str_replace(
                ['¤', '¦', '¨', '´', '¸', '¼', '½', '¾'],
                ['€', 'Š', 'š', 'Ž', 'ž', 'Œ', 'œ', 'Ÿ'],
                $data
            );
        }
    }

    public static function pcreLastErrorMessage(int $code): string
    {
        return preg_last_error_msg();
    }

    public const SIMPLE_DATE = "Y-m-d\TH:i:sP";

    protected string $dateFormat = 'Y-m-d H:i:s.v';

    private function formatDate(\DateTimeInterface $date): string
    {
        // in case the date format isn't custom then we defer to the custom DateTimeImmutable
        // formatting logic, which will pick the right format based on whether useMicroseconds is on
        if ($this->dateFormat === self::SIMPLE_DATE && $date instanceof DateTimeImmutable) {
            return (string) $date;
        }

        return $date->format($this->dateFormat);
    }
}