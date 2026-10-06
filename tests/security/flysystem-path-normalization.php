<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/public/wp-content/plugins/knit-pay/secondary-packages/vendor/autoload.php';

use League\Flysystem\CorruptedPathDetected;
use League\Flysystem\PathTraversalDetected;
use League\Flysystem\WhitespacePathNormalizer;

$normalizer = new WhitespacePathNormalizer();
$failures = 0;
$checks = 0;

foreach ([
    'normal path' => ['uploads/file.txt', 'uploads/file.txt'],
    'Windows separators' => ['uploads\\file.txt', 'uploads/file.txt'],
    'relative path' => ['uploads/old/../file.txt', 'uploads/file.txt'],
    'Unicode filename' => ['uploads/caf' . "\xC3\xA9" . '.txt', 'uploads/caf' . "\xC3\xA9" . '.txt'],
] as $label => [$path, $expected]) {
    ++$checks;
    if ($normalizer->normalizePath($path) !== $expected) {
        fwrite(STDERR, "FAIL: {$label}\n");
        ++$failures;
    }
}

foreach ([
    'NUL' => "uploads/file\x00.txt",
    'control character' => "uploads/file\x01.txt",
    'malformed UTF-8' => "uploads/file\xFF.txt",
    'malformed UTF-8 with NUL' => "uploads/file\xFF\x00.txt",
    'malformed UTF-8 with control character' => "uploads/file\xFF\x01.txt",
] as $label => $path) {
    ++$checks;
    try {
        $normalizer->normalizePath($path);
        fwrite(STDERR, "FAIL: {$label} was accepted\n");
        ++$failures;
    } catch (CorruptedPathDetected $expected) {
        // Reject malformed and control-character paths before they reach an adapter.
    }
}

foreach ([new WhitespacePathNormalizer(), new WhitespacePathNormalizer(false)] as $index => $instance) {
    ++$checks;
    try {
        $instance->normalizePath($index === 0 ? '../file.txt' : 'uploads/../file.txt');
        fwrite(STDERR, "FAIL: forbidden traversal was accepted\n");
        ++$failures;
    } catch (PathTraversalDetected $expected) {
        // Preserve both traversal policies.
    }
}

echo "{$checks} checks, {$failures} failures\n";
exit($failures === 0 ? 0 : 1);
