<?php

namespace Hobosoft\Logger;

class DumpVar
{
    public static function dump(mixed $var): string
    {
        $lines = [];

        return implode(PHP_EOL, $lines);
    }

    public static function print_rr2(mixed $array, int $depth = 0) {
        if(is_array($array) === false) {
            $array = json_decode(json_encode($array), true);
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($depth > 0) {
                    $array[$key] = self::print_rr2($value, $depth - 1);
                } else {
                    unset($array[$key]);
                }
            }
        }
        return implode(PHP_EOL, $array);
    }

    public static function print_rr(mixed $thing, $stack = [], $returnString = false, $maxDepth = 10, $depth = 0): string|null
    {
        $ret = '';
        $type = 'array';
        if (is_object($thing)) {
            $vars = get_object_vars($thing);
            $type = get_class($thing);
        } else {
            $vars = $thing;
        }

        $prefix = str_repeat("  ", $depth);

        if (is_array($vars)) {
            $stack[] = $thing;
            $count = $type === 'array' ? '(' . count($vars) . ')' : '';
            if ($depth <= $maxDepth) {
                $ret .= "$type$count => [";
                //echo "$type$count => [";

                if (count($vars) == 0) {
                    $ret .= "]\n";
                    //echo "]\n";
                }
                else {
                    foreach ($thing as $k => $v) {
                        if (!empty($stack) && in_array($v, $stack)) {
                            $ret .= "\n$prefix  $k => [circular]\n";
                            //echo "\n$prefix  $k => [circular]\n";
                        } else {
                            $ret .= "\n$prefix  $k => ";
                            //echo "\n$prefix  $k => ";
                            self::print_rr($v, $stack, $returnString, $maxDepth, $depth + 1);
                        }
                    }
                    $ret .= "$prefix]\n";
                    //echo "$prefix]\n";
                }
            } else {
                $ret .= "$type$count => [omitted]";
                //echo "$type$count => [omitted]";
            }
            if($returnString) {
                return $ret;
            }
            echo $ret;
            return false;
        }

        var_dump($thing) . "\n";

        if($returnString) {
            return $ret;
        }
        echo $ret;
        return false;
    }
}