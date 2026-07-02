<?php
/**
 * Config File For QuanLyThuPhi Backend API
 */

// Http Url
// Cleanly get the path part of the URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Get the directory of the script
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// If the app is in the root directory, scriptDir might be '/' or '\'. Normalize to empty.
if ($scriptDir === '/') $scriptDir = '';

// Extract the path after the script directory
$path = substr($uri, strlen($scriptDir));
// Ensure it starts with a /
if (empty($path) || $path[0] !== '/') {
    $path = '/' . $path;
}
define('HTTP_URL', $path);

// Define Path
define('SCRIPT', str_replace('\\', '/', rtrim(__DIR__, '/')) . '/');
define('SYSTEM', SCRIPT . 'System/');
define('CONTROLLERS', SCRIPT . 'controllers/');
define('MODELS', SCRIPT . 'models/');
define('UPLOAD', SCRIPT . 'uploads/');

// Config Database
define('DATABASE', [
    'Port'   => getenv('DB_PORT') ?: '3306',
    'Host'   => getenv('DB_HOST') ?: 'localhost',
    'Driver' => 'PDO',
    'Name'   => getenv('DB_NAME') ?: 'student_fee_management',
    'User'   => getenv('DB_USER') ?: 'root',
    'Pass'   => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'Prefix' => ''
]);

define('DB_PREFIX', '');
