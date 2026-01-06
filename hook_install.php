<?php

declare(strict_types=1);

// Create data directory if it doesn't exist
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Log installation
file_put_contents(
    $dataDir . '/plugin.log',
    "Payment vs Expense Report plugin installed on " . date('Y-m-d H:i:s') . "\n",
    FILE_APPEND
);
