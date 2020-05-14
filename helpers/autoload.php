<?php

/**
 * @param string $class
 *
 * @throws \RuntimeException
 */
function autoload(string $class)
{
    $class = ltrim($class, '\\');
    $path = __DIR__ . '/../'.str_replace('\\', '/', $class) . '.php';

    if (!file_exists($path)) {
        throw new \RuntimeException(sprintf('Class "%s" doesn\'t exists', $class));
    }

    require_once $path;
}

spl_autoload_register('autoload');
