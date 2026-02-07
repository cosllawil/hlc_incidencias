<?php
declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $v = getenv($key);
        if ($v === false || $v === '') {
            return $default;
        }
        return $v;
    }
}

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'hlc_incidencias'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));
define('BASE_URL', env('BASE_URL', '/hlc_incidencias'));

define('APP_KEY', env('APP_KEY', ''));
define('RENIEC_TOKEN', env('RENIEC_TOKEN', ''));

@date_default_timezone_set((string) env('TIMEZONE', 'America/Lima'));
