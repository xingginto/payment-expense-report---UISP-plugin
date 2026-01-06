<?php

declare(strict_types=1);

// Plugin removed - cleanup if needed
$dataDir = __DIR__ . '/data';
if (is_dir($dataDir)) {
    // Optionally clean up data directory
    // array_map('unlink', glob("$dataDir/*.*"));
    // rmdir($dataDir);
}
