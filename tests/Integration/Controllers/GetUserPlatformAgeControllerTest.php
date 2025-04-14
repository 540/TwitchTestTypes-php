<?php

declare(strict_types=1);

namespace TwitchAnalytics\Tests\Integration\Controllers;

use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Controllers\GetUserPlatformAge\GetUserPlatformAgeController;
use TwitchAnalytics\Application\Services\UserAccountService;
use TwitchAnalytics\Infrastructure\Repositories\ApiUserRepository;
use TwitchAnalytics\Infrastructure\ApiClient\FakeTwitchApiClient;
use TwitchAnalytics\Controllers\GetUserPlatformAge\UserNameValidator;
use TwitchAnalytics\Domain\Time\TimeProvider;

class GetUserPlatformAgeControllerTest extends TestCase
{
    private GetUserPlatformAgeController $controller;
    private TimeProvider $timeProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $apiClient = new FakeTwitchApiClient();
        $repository = new ApiUserRepository($apiClient);
        $this->timeProvider = Mockery::mock(TimeProvider::class);
        $service = new UserAccountService($repository, $this->timeProvider);
        $validator = new UserNameValidator();

        $this->controller = new GetUserPlatformAgeController($service, $validator);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function gets400ForInvalidUsername(): void
    {
        $_GET['name'] = 'ab';

        $response = $this->controller->__invoke();

        $this->assertEquals(400, http_response_code());
        $this->assertJsonStringEqualsJsonString(
            '{"error":"INVALID_REQUEST","message":"Name must be at least 3 characters long","status":400}',
            $response
        );
    }

    /**
     * @test
     */
    public function gets404ErrorForNonExistingtUser(): void
    {
        $_GET['name'] = 'NonExistentUser';

        $response = $this->controller->__invoke();

        $this->assertEquals(404, http_response_code());
        $this->assertJsonStringEqualsJsonString(
            '{"error":"USER_NOT_FOUND","message":"No user found with given name: NonExistentUser","status":404}',
            $response
        );
    }

    /**
     * @test
     */
    public function getsUserAgeForExistingUser(): void
    {
        $_GET['name'] = 'Ninja';

        $this->timeProvider->allows('now')->andReturns(new DateTime('2025-01-01T00:00:00Z'));

        $response = $this->controller->__invoke();
        $responseData = json_decode($response, true);

        $this->assertEquals(200, http_response_code());
        $this->assertJsonStringEqualsJsonString(
            '{"name":"Ninja","created_at":"2011-11-20T00:00:00Z","days_since_creation":4791}',
            json_encode($responseData)
        );
    }
}
