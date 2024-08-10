<?php

namespace Hobosoft\Logger\Contracts;

use Psr\Log\LogLevel as PsrLogLevel;

enum LogLevel: int
{
    case Debug = 100;
    case Info = 200;
    case Notice = 250;
    case Warning = 300;
    case Error = 400;
    case Critical = 500;
    case Alert = 550;
    case Emergency = 600;

    public function toPsr(): string
    {
        return match ($this) {
            self::Debug => PsrLogLevel::DEBUG,
            self::Info => PsrLogLevel::INFO,
            self::Notice => PsrLogLevel::NOTICE,
            self::Warning => PsrLogLevel::WARNING,
            self::Error => PsrLogLevel::ERROR,
            self::Critical => PsrLogLevel::CRITICAL,
            self::Alert => PsrLogLevel::ALERT,
            self::Emergency => PsrLogLevel::EMERGENCY,
        };
    }

    public static function fromPsr(string $name): LogLevel
    {
        return match ($name) {
            PsrLogLevel::DEBUG => self::Debug,
            PsrLogLevel::INFO => self::Info,
            PsrLogLevel::NOTICE => self::Notice,
            PsrLogLevel::WARNING => self::Warning,
            PsrLogLevel::ERROR => self::Error,
            PsrLogLevel::CRITICAL => self::Critical,
            PsrLogLevel::ALERT => self::Alert,
            PsrLogLevel::EMERGENCY => self::Emergency,
        };
    }

    public static function fromString(string $name): LogLevel
    {
        return self::fromPsr(strtolower($name));
    }

    public static function fromInt(int $n): ?LogLevel
    {
        return self::tryFrom($n);
    }

    public static function fromMixed(int|string|LogLevel $level): LogLevel
    {
        if (is_int($level)) {
            return LogLevel::fromInt($level);
        } elseif (is_string($level)) {
            return LogLevel::fromPsr($level);
        }
        return $level;
    }
    
    public static function normalize(mixed $level): LogLevel
    {
        return is_string($level) ? LogLevel::fromPsr($level) : $level;
    }

    public function getName(?LogLevel $n = null): string
    {
        return match ($n ?? $this) {
            self::Debug => 'DEBUG',
            self::Info => 'INFO',
            self::Notice => 'NOTICE',
            self::Warning => 'WARNING',
            self::Error => 'ERROR',
            self::Critical => 'CRITICAL',
            self::Alert => 'ALERT',
            self::Emergency => 'EMERGENCY',
        };
    }

    public function includes(LogLevel $level): bool
    {
        return $this->value <= $level->value;
    }

    public function isHigherThan(LogLevel $level): bool
    {
        return $this->value > $level->value;
    }

    public function isLowerThan(LogLevel $level): bool
    {
        return $this->value < $level->value;
    }
}
