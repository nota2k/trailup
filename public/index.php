<?php

// Réduire les avertissements de dépréciation PHP 8.3+
// E_STRICT (2048) est déprécié en PHP 8.3+, on utilise la valeur numérique
error_reporting(E_ALL & ~E_DEPRECATED & ~2048);

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
