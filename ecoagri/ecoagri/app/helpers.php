<?php
// Small functions used everywhere in the views and controllers.

function config(string $key, $default = null)
{
    return $GLOBALS['config'][$key] ?? $default;
}

/** Escape text before printing it in HTML (protects against XSS). */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Base folder of the app, e.g. "/ecoagri/public" under XAMPP or "" on a virtual host. */
function base_path(): string
{
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return $dir === '.' ? '' : $dir;
}

function url(string $path = '/'): string
{
    return base_path() . '/index.php?route=' . rawurlencode('/' . ltrim($path, '/'));
}

function asset(string $path): string
{
    return base_path() . '/assets/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}
