<?php

declare(strict_types=1);

namespace ImageCompressor;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\ConsoleHandler;
use Monolog\Logger as MonoLogger;
use Monolog\Formatter\LineFormatter;

/**
 * Central logger using Monolog.
 */
final class Logger
{
    private static ?MonoLogger $logger = null;

    private function __construct()
    {
        // Prevent instantiation
    }
// rewrote this part

    public static function get(): MonoLogger
    {
        if (self::$logger !== null) {
            return self::$logger;
        }

        $config = Config::getInstance();
        $logPath = $config->getLogPath();

        $logger = new MonoLogger('ImageCompressor');

        // File handler
        $fileHandler = new StreamHandler($logPath, $config->isProd() ? MonoLogger::WARNING : MonoLogger::DEBUG);
        $fileFormatter = new LineFormatter(null, null, true, true);
        $fileHandler->setFormatter($fileFormatter);
        $logger->pushHandler($fileHandler);

        // Console handler (only for non‑prod)
        if (!$config->isProd()) {
            $consoleHandler = new ConsoleHandler(MonoLogger::DEBUG);
            $logger->pushHandler($consoleHandler);
        }

        self::$logger = $logger;
        return $logger;
// rewrote this part
    }
}