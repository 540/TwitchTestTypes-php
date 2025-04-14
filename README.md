# Twitch Analytics PHP Project

A PHP-based REST API that provides Twitch user account analytics, focusing on account age calculation based on display names. Built with Lumen, a fast micro-framework by Laravel.

## Requirements

- PHP 8.3 or higher
- Composer
- PHP extensions: json

## Installation

1. Clone the repository
2. Install dependencies:
```bash
composer install
```
3. Copy the environment file:
```bash
cp .env.example .env
```

## Development Setup

### First Time Setup

1. Install PHP 8.3 or higher:
   ```bash
   # macOS with Homebrew
   brew install php@8.3

   # Ubuntu/Debian
   sudo apt-get install php8.3
   ```

2. Install Composer:
   ```bash
   # Download installer
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

   # Install globally
   sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
   ```

3. Clone and setup the project:
   ```bash
   # Clone repository
   git clone <repository-url>
   cd twitch-analytics

   # Install dependencies
   composer install

   # Setup environment
   cp .env.example .env
   ```

### Running the Application

There are several ways to run the application:

1. **Using Composer Script** (Recommended for development):
   ```bash
   composer start
   ```
   This will start the server at http://localhost:8000

2. **Using PHP's Built-in Server**:
   ```bash
   php -S localhost:8000 -t public
   ```

3. **Using Docker** (if available):
   ```bash
   docker-compose up -d
   ```

### Development Commands

```bash
# Run all tests
composer test

# Run specific test suites
./vendor/bin/phpunit tests/Unit
./vendor/bin/phpunit tests/Integration
./vendor/bin/phpunit tests/E2E

# Generate test coverage report
composer test:coverage

# Check code style
composer cs-check

# Fix code style automatically
composer cs-fix
```

### Debugging

1. **Enable Debug Mode**:
   In your `.env` file, set:
   ```
   APP_DEBUG=true
   ```

2. **View Logs**:
   - Application logs are in `storage/logs/lumen.log`
   - PHP errors are logged based on your `php.ini` configuration

3. **Using Xdebug**:
   - Install Xdebug extension
   - Configure your IDE for debugging
   - Set breakpoints and debug as needed

### Common Issues

1. **Permission Issues**:
   ```bash
   # Fix storage permissions
   chmod -R 777 storage
   ```

2. **Composer Issues**:
   ```bash
   # Clear composer cache
   composer clear-cache

   # Rebuild autoloader
   composer dump-autoload
   ```

3. **Server Port Already in Use**:
   ```bash
   # Find process using port 8000
   lsof -i :8000

   # Kill the process
   kill -9 <PID>
   ```

## Running the Application

Start the PHP development server:
```bash
composer start
```
Or manually:
```bash
php -S localhost:8000 -t public
```

## API Endpoints

### Get User Platform Age

**Endpoint:** `GET /api/users/platform-age`

**Parameters:**
- `name` (required): The name of the Twitch user (3-25 characters)

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/users/platform-age?name=Ninja"
```

**Success Response (200 OK):**
```json
{
    "name": "Ninja",
    "days_since_creation": 4482,
    "created_at": "2011-11-20T00:00:00Z"
}
```

**Validation Rules:**
- Name parameter is required
- Name must be between 3 and 25 characters long
- Name is case-insensitive

## Project Structure

```
src/
├── Application/
│   └── Services/
│       └── UserAccountService.php
├── Controllers/
│   └── GetUserPlatformAge/
│       ├── GetUserPlatformAgeController.php
│       ├── UserNameValidator.php
│       └── ValidationException.php
├── Domain/
│   ├── Exceptions/
│   │   ├── ApplicationException.php
│   │   └── UserNotFoundException.php
│   ├── Interfaces/
│   │   └── UserRepositoryInterface.php
│   └── Models/
│       └── User.php
└── Infrastructure/
    ├── ApiClient/
    │   ├── FakeTwitchApiClient.php
    │   └── TwitchApiClientInterface.php
    └── Repositories/
        └── ApiUserRepository.php
```

## Testing

The project includes three types of tests:

1. **Unit Tests**: Test individual components in isolation
```bash
./vendor/bin/phpunit tests/Unit
```

2. **Integration Tests**: Test component interactions
```bash
./vendor/bin/phpunit tests/Integration
```

3. **E2E Tests**: Test complete HTTP request/response cycle
```bash
./vendor/bin/phpunit tests/E2E
```

## Error Handling

The API returns appropriate HTTP status codes and error messages:

- 400 Bad Request: Invalid input parameters
- 404 Not Found: User not found
- 500 Internal Server Error: Unexpected errors

**Error Response Format:**
```json
{
    "error": "ERROR_TYPE",
    "message": "Detailed error message",
    "status": 400
}
```

## Framework: Lumen

This project uses Lumen, a micro-framework by Laravel. Here are the key benefits it provides:

### Why Lumen?

1. **Performance**: Lumen is a lightweight version of Laravel, optimized for microservices and APIs
2. **Testing Support**: Built-in testing utilities for HTTP requests, responses, and mocking
3. **Dependency Injection**: Robust container for managing service dependencies
4. **Routing**: Clean and efficient routing system
5. **Middleware**: Easy to add request/response transformations
6. **Error Handling**: Comprehensive exception handling system

### Key Framework Features Used

1. **Service Container**:
   - Automatic dependency injection
   - Service registration in `bootstrap/app.php`
   - Easy service mocking in tests

2. **Request Handling**:
   - Clean access to request parameters
   - Built-in request validation
   - JSON response formatting

3. **Testing Utilities**:
   - HTTP request simulation
   - Response assertions
   - Service mocking
   - Isolated test environment

### Migration to Lumen

The project was migrated from vanilla PHP to Lumen to improve:

1. **Code Organization**:
   - Clear service registration
   - Standardized request/response handling
   - Better dependency management

2. **Testing**:
   - End-to-end HTTP testing
   - Better service mocking
   - More reliable assertions

3. **Maintainability**:
   - Framework-provided best practices
   - Standard service container
   - Consistent error handling

To migrate a similar project to Lumen:

1. Add Lumen dependencies:
```json
{
    "require": {
        "laravel/lumen-framework": "^10.0",
        "vlucas/phpdotenv": "^5.6"
    }
}
```

2. Create Lumen structure:
   - `bootstrap/app.php` for application setup
   - `routes/api.php` for route definitions
   - `.env` for configuration

3. Update controllers:
   - Extend `Laravel\Lumen\Routing\Controller`
   - Use `JsonResponse` for responses
   - Use dependency injection
   - Replace `$_GET`/`$_POST` with Request object

Example controller update:
```php
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $name = $request->get('name');  // Instead of $_GET['name']
        return new JsonResponse(['data' => $result]);
    }
}
```

4. Register services:
   - Use `$app->singleton()` for service registration
   - Configure service container in `bootstrap/app.php`
   - Set up proper dependency injection

5. Update tests:
   - Use Lumen's `TestCase` for e2e tests
   - Update integration tests to use Request/Response objects
   - Implement proper service mocking

Example test migration:
```php
// Before
public function testEndpoint(): void
{
    $_GET['name'] = 'testuser';
    $response = $this->controller->__invoke();
    $this->assertEquals(200, http_response_code());
    $this->assertJsonStringEqualsJsonString(
        '{"data":"value"}',
        $response
    );
}

// After
public function testEndpoint(): void
{
    $request = new Request();
    $request->query->set('name', 'testuser');

    $response = $this->controller->__invoke($request);
    $responseData = json_decode($response->getContent(), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(['data' => 'value'], $responseData);
}
```

Key testing changes:
- Replace global `$_GET`/`$_POST` with `Request` objects
- Use `JsonResponse` methods instead of raw HTTP functions
- Use proper assertion methods for response status and content
- Take advantage of Lumen's testing utilities for cleaner tests

## Contributing

1. Fork the repository
2. Create your feature branch
3. Write tests for your changes
4. Ensure all tests pass
5. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.
