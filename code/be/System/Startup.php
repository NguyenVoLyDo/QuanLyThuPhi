<?php

// Autoload classes based on namespace
function autoload($class) {
    $file = SYSTEM . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file))
        require_once $file;
    else
        throw new Exception(sprintf('Class { %s } Not Found! (looked in: %s)', $class, $file));
}

spl_autoload_register('autoload');

// Load helper functions
require_once SYSTEM . 'Helper/public.php';
