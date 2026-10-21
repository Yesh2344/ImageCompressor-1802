<?php

declare(strict_types=1);

namespace ImageCompressor;

use Dotenv\Dotenv;
use RuntimeException;

/**
 * Loads environment variables and provides typed getters.
 */
final class Config
{
    private static ?self $instance = null;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function getLogPath(): string
    {
        $path = $_ENV['LOG_PATH'] ?? 'logs/app.log';
        return $this->ensureWritableDirectory($path);
    }

    public function getJpegQuality(): int
    {
        $quality = (int)($_ENV['JPEG_QUALITY'] ?? 85);
        if ($quality < 0 || $quality > 100) {
            throw new RuntimeException('JPEG_QUALITY must be between 0 and 100.');
        }
        return $quality;
    }

    public function getPngCompressionLevel(): int
    {
        $level = (int)($_ENV['PNG_COMPRESSION_LEVEL'] ?? 6);
        if ($level < 0 || $level > 9) {
            throw new RuntimeException('PNG_COMPRESSION_LEVEL must be between 0 and 9.');
        }
        return $level;
    }

    public function isProd(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'dev') === 'prod';
    }

    /**
     * Ensures the directory for the log file exists and is writable.
     */
    private function ensureWritableDirectory(string $filePath): string
    {
        $dir = dirname($filePath);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException("Unable to create log directory: {$dir}");
        }
        if (!is_writable($dir)) {
            throw new RuntimeException("Log directory is not writable: {$dir}");
        }
        return $filePath;
    }
}