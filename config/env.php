<?php
/**
 * Environment Configuration Helper
 * 
 * Include this file at the top of any PHP page to enable
 * error reporting during development and control behavior
 * on production (AWS).
 * 
 * On AWS, set the environment variable APP_ENV=production
 * to suppress detailed error messages for end users.
 */

$appEnv = getenv('APP_ENV') ?: 'development';

if ($appEnv === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../error.log');
}
?>
