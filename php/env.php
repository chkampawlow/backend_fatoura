<?php

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        throw new Exception(".env file not found: " . $path);
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        $line = trim($line);

        // ignore comments
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // split key=value
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');

        $key = trim($key);
        $value = trim($value);

        // remove quotes if present
        $value = trim($value, "\"'");

        // set env
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv("$key=$value");
    }
}