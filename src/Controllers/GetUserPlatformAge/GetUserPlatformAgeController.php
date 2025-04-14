<?php

declare(strict_types=1);

namespace TwitchAnalytics\Controllers\GetUserPlatformAge;

use TwitchAnalytics\Application\Services\UserAccountService;
use TwitchAnalytics\Domain\Exceptions\UserNotFoundException;
use TwitchAnalytics\Domain\Exceptions\ApplicationException;

class GetUserPlatformAgeController
{
    private int $statusCode = 200;

    public function __construct(
        private UserAccountService $userAccountService,
        private UserNameValidator $userNameValidator
    ) {
    }

    public function __invoke(): string
    {
        try {
            $name = $this->userNameValidator->validate($_GET['name'] ?? null);
            $result = $this->userAccountService->getAccountAge($name);

            return $this->sendJsonResponse($result);
        } catch (ValidationException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (UserNotFoundException $e) {
            return $this->sendErrorResponse($e->getMessage(), 404);
        } catch (ApplicationException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('An unexpected error occurred', 500);
        }
    }

    private function sendJsonResponse(array $data, int $statusCode = 200): string
    {
        $this->statusCode = $statusCode;
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    private function sendErrorResponse(string $message, int $statusCode): string
    {
        $response = [
            'error' => $this->getErrorType($statusCode),
            'message' => $message,
            'status' => $statusCode
        ];

        return $this->sendJsonResponse($response, $statusCode);
    }

    private function getErrorType(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'INVALID_REQUEST',
            404 => 'USER_NOT_FOUND',
            default => 'INTERNAL_ERROR',
        };
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
