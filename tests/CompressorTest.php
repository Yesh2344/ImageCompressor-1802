<?php

declare(strict_types=1);

namespace ImageCompressor\Tests;

use ImageCompressor\{Config, Logger, ImageProcessor, Compressor};
use PHPUnit\Framework\TestCase;
use Monolog\Logger as MonoLogger;

/**
 * @covers \ImageCompressor\Compressor
 */
final class CompressorTest extends TestCase
{
    private string $fixtureDir;
    private string $tempDir;

    protected function setUp(): void
    {
        $this->fixtureDir = __DIR__ . '/fixtures';
        $this->tempDir    = sys_get_temp_dir() . '/imgcompress_test_' . uniqid();
        mkdir($this->tempDir, 0755, true);
        // Copy fixtures to temp dir
        foreach (glob($this->fixtureDir . '/*.{jpg,png}', GLOB_BRACE) as $file) {
            copy($file, $this->tempDir . '/' . basename($file));
        }
    }

    protected function tearDown(): void
    {
        // Cleanup temp directory
        array_map('unlink', glob($this->tempDir . '/*'));
        rmdir($this->tempDir);
    }

    public function testCompressSingleJpeg(): void
    {
        $jpegFile = $this->tempDir . '/sample.jpg';
        $this->assertFileExists($jpegFile);

        $compressor = $this->createCompressor();
        $compressor->compress($jpegFile);

        $compressed = $this->tempDir . '/sample.min.jpg';
        $this->assertFileExists($compressed);
        $this->assertLessThan(
            filesize($jpegFile),
            filesize($compressed),
            'Compressed JPEG should be smaller than original.'
        );
    }

    public function testCompressDirectoryRecursively(): void
    {
        $compressor = $this->createCompressor();
        $compressor->compressDirectory($this->tempDir, true);

        $compressedJpg = $this->tempDir . '/sample.min.jpg';
        $compressedPng = $this->tempDir . '/logo.min.png';

        $this->assertFileExists($compressedJpg);
        $this->assertFileExists($compressedPng);
    }

    private function createCompressor(): Compressor
    {
        // Use a logger that writes to null to keep test output clean
        $nullLogger = new MonoLogger('null');
        $nullLogger->pushHandler(new \Monolog\Handler\NullHandler());

        $config = Config::getInstance();
        $processor = new ImageProcessor(
            $config->getJpegQuality(),
            $config->getPngCompressionLevel()
        );

        return new Compressor($processor, $nullLogger);
    }
}