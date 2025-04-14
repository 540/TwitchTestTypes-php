<?php

/** @var Laravel\Lumen\Routing\Router $router */

use TwitchAnalytics\Controllers\GetUserPlatformAge\GetUserPlatformAgeController;

$router->get('/users/platform-age', [
    'uses' => GetUserPlatformAgeController::class,
    'as' => 'users.platform-age'
]);
