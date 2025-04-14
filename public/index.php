<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use TwitchAnalytics\Controllers\GetUserPlatformAge\GetUserPlatformAgeController;
use TwitchAnalytics\Application\Services\UserAccountService;
use TwitchAnalytics\Infrastructure\Repositories\ApiUserRepository;
use TwitchAnalytics\Infrastructure\ApiClient\FakeTwitchApiClient;
use TwitchAnalytics\Controllers\GetUserPlatformAge\UserNameValidator;
use TwitchAnalytics\Infrastructure\Time\SystemTimeProvider;

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Basic routing
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Dependency injection
$apiClient = new FakeTwitchApiClient();
$repository = new ApiUserRepository($apiClient);
$service = new UserAccountService($repository);
$validator = new UserNameValidator();
$controller = new GetUserPlatformAgeController($service, $validator);

// Route handling
if ($method === 'GET' && $path === '/api/users/platform-age') {
    echo $controller();
} else {
    http_response_code(404);
    echo json_encode([
        'error' => 'NOT_FOUND',
        'message' => 'Route not found',
        'status' => 404
    ]);
}
