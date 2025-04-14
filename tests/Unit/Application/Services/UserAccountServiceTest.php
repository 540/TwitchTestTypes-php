<?php

namespace TwitchAnalytics\Tests\Unit\Application\Services;


use Mockery;
use TwitchAnalytics\Application\Services\UserAccountService;
use PHPUnit\Framework\TestCase;
use TwitchAnalytics\Domain\Exceptions\UserNotFoundException;
use TwitchAnalytics\Domain\Interfaces\UserRepositoryInterface;

class UserAccountServiceTest extends TestCase
{
    private UserRepositoryInterface $userRepository;
    private UserAccountService $userAccountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->userAccountService = new UserAccountService($this->userRepository);
    }

    public function testGetAccountAgeThrowsExceptionWhenUserNotFound(): void
    {
        $displayName = 'NonExistentUser';
        $this->userRepository->expects('findByDisplayName')
            ->with($displayName)
            ->andReturnNull();

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No user found with given name: {$displayName}");

        $this->userAccountService->getAccountAge($displayName);
    }
}
