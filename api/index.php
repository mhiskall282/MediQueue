<?php

declare(strict_types=1);

/**
 * Vercel Serverless Function Bridge for Laravel 12
 * 
 * Prepares ephemeral /tmp filesystem directories required by Laravel's
 * cache, compiled views, session files, and log channels.
 */

$storageDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/cache/data',
    '/tmp/sessions',
    '/tmp/logs',
    '/tmp/framework/sessions',
    '/tmp/framework/views',
    '/tmp/framework/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Forward request to Laravel's standard front controller
require __DIR__ . '/../public/index.php';
