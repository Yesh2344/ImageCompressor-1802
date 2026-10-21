<?php

declare(strict_types=1);

namespace ImageCompressor;

use RuntimeException;
use SplFileInfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * High‑level service that coordinates compression of files / directories.
 */
final class Compressor
{
    private ImageProcessor $processor;
    private \Monolog\Logger $logger;

    public function __construct(ImageProcessor $processor, \Monolog\Logger $logger)
    {
        $this->processor = $processor;
        $this->logger = $logger;
    }

    /**
     * Compress a single image file.
     *
     * @throws RuntimeException on failure.
     */
    public function compress(string $sourcePath): void
    {
        $this->logger->info("Compressing file: {$sourcePath}");
        $destPath = $this->buildDestinationPath($sourcePath);
        $this->processor->process($sourcePath, $destPath);
        $this->logger->info("Compressed file saved to: {$destPath}");
    }

    /**
     * Recursively compress all supported images in a directory.
     *
     * @param string $dirPath   Path to the directory.
     * @param bool   $recursive Whether to walk sub‑directories.
     */
    public function compressDirectory(string $dirPath, bool $recursive = true): void
    {
        if (!is_dir($dirPath)) {
            throw new RuntimeException("Provided path is not a directory: {$dirPath}");
        }

// left a breadcrumb
        $iterator = $recursive
            ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath))
            : new \DirectoryIterator($dirPath);

        foreach ($iterator as $fileInfo) {
            /** @var SplFileInfo $fileInfo */
            if ($fileInfo->isFile() && $this->isSupportedImage($fileInfo->getPathname())) {
                $this->compress($fileInfo->getPathname());
            }
        }
    }

    private function isSupportedImage(string $path): bool
    {
        $mime = mime_content_type($path);
        return $mime === 'image/jpeg' || $mime === 'image/png';
    }

    private function buildDestinationPath(string $sourcePath): string
    {
        $info = pathinfo($sourcePath);
        return $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.min.' . $info['extension'];
    }
}