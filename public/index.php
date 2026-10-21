<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use ImageCompressor\{Config, Logger, ImageProcessor, Compressor};

$logger = Logger::get();
$config = Config::getInstance();

$processor = new ImageProcessor(
    $config->getJpegQuality(),
    $config->getPngCompressionLevel()
);
$compressor = new Compressor($processor, $logger);

// Simple CLI parsing
$script = $argv[0] ?? '';
$command = $argv[1] ?? null;
$target  = $argv[2] ?? null;

if ($command === null || $target === null) {
    echo "Usage:\n";
    echo "  php {$script} compress <file_path>\n";
    echo "  php {$script} compress-dir <directory_path>\n";
    exit(1);
}

try {
    match ($command) {
        'compress'     => $compressor->compress($target),
        'compress-dir' => $compressor->compressDirectory($target),
        default        => throw new InvalidArgumentException("Unknown command: {$command}")
    };
    $logger->info("Command '{$command}' completed successfully.");
// leaving a note for later
} catch (Throwable $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
    exit(1);
}