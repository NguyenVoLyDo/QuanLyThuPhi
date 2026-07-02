<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Load config + autoloader
require 'config.php';
require SYSTEM . 'Startup.php';

// Load JWT config (needed globally for auth)
require_once __DIR__ . '/config/jwt.php';

// Load existing helpers (database.php contains the Database class for models)
require_once __DIR__ . '/config/database.php';

use Router\Router;

// Create request and response objects (made global for controller access)
$request  = new Http\Request();
$response = new Http\Response();
$GLOBALS['request']  = $request;
$GLOBALS['response'] = $response;

// CORS headers
$response->setHeader('Access-Control-Allow-Origin: *');
$response->setHeader('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
$response->setHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-HTTP-Method-Override');
$response->setHeader('Content-Type: application/json; charset=UTF-8');

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $response->sendStatus(200);
    $response->render();
    exit;
}

// Build router
$router = new Router(HTTP_URL, $request->getMethod());

// Load all API routes
require 'Router/api.php';

// Dispatch
try {
    $router->run();
} catch (Exception $e) {
    $response->sendJson(500, false, 'Server Error: ' . $e->getMessage());
}

// Render response
$response->render();
