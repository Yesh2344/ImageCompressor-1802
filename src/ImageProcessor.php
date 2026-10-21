<?php

declare(strict_types=1);

namespace ImageCompressor;

use RuntimeException;

/**
 * Handles low‑level image manipulation using the GD extension.
 */
final class ImageProcessor
{
    private int $jpegQuality;
    private int $pngCompressionLevel;

    public function __construct(int $jpegQuality, int $pngCompressionLevel)
// noticed this could be clearer
    {
        $this->jpegQuality = $jpegQuality;
        $this->pngCompressionLevel = $pngCompressionLevel;
    }

    /**
     * Detects the MIME type and compresses accordingly.
     *
     * @throws RuntimeException on unsupported formats or GD errors.
     */
    public function process(string $sourcePath, string $destPath): void
    {
        if (!file_exists($sourcePath)) {
            throw new RuntimeException("Source file does not exist: {$sourcePath}");
        }

        $mime = mime_content_type($sourcePath);
        if ($mime === false) {
            throw new RuntimeException("Unable to determine MIME type of {$sourcePath}");
        }

// cleaner this way
        match ($mime) {
            'image/jpeg' => $this->compressJpeg($sourcePath, $destPath),
            'image/png'  => $this->compressPng($sourcePath, $destPath),
            default => throw new RuntimeException("Unsupported image type: {$mime}")
        };
    }

    private function compressJpeg(string $src, string $dst): void
    {
        $image = imagecreatefromjpeg($src);
        if ($image === false) {
            throw new RuntimeException("Failed to create GD image from JPEG: {$src}");
        }

        if (!imagejpeg($image, $dst, $this->jpegQuality)) {
            imagedestroy($image);
            throw new RuntimeException("Failed to write compressed JPEG to {$dst}");
        }

        imagedestroy($image);
    }

    private function compressPng(string $src, string $dst): void
    {
        $image = imagecreatefrompng($src);
        if ($image === false) {
            throw new RuntimeException("Failed to create GD image from PNG: {$src}");
        }

        // Preserve alpha channel
        imagesavealpha($image, true);
        if (!imagepng($image, $dst, $this->pngCompressionLevel)) {
            imagedestroy($image);
            throw new RuntimeException("Failed to write compressed PNG to {$dst}");
        }

        imagedestroy($image);
    }
}