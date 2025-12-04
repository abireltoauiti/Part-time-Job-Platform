<?php

// Increase maximum execution time for local dev so container compilation
// or other long boot steps do not hit the default 30s limit.
// This change is safe for local development but should NOT be relied on
// as a production setting. For production, configure `php.ini` / FPM.
ini_set('max_execution_time', '0');

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
