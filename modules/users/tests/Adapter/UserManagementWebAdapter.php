<?php
namespace Starbug\Users\Tests\Adapter;

use PHPUnit\Framework\Assert;
use Starbug\Testing\WebAdapter;

/**
 * HTTP-based protocol adapter for user management.
 *
 * Translates user CRUD operations into HTTP requests
 * via the underlying driver.
 */
class UserManagementWebAdapter extends WebAdapter implements UserManagementAdapterInterface {

  public function createUser(array $data): void {
    $defaults = [
      'password_confirm' => $data['password'] ?? ''
    ];
    $this->driver->submitForm('/admin/users/create', $data + $defaults);
  }

  public function listUsers(): void {
    $this->visit('/admin/users');
  }

  public function assertUserCreated(): void {
    Assert::assertEquals('/admin/users', $this->driver->getCurrentPath());
  }

  public function assertValidationFailed(array $expectedErrors): void {
    $errors = $this->driver->getFormErrors();
    foreach ($expectedErrors as $field => $message) {
      Assert::assertArrayHasKey($field, $errors, "Expected error for field {$field}");
      Assert::assertStringContainsString($message, $errors[$field]);
    }
  }

  public function assertOnUsersList(): void {
    Assert::assertEquals('/admin/users', $this->driver->getCurrentPath());
  }

  public function assertUserExists(array $criteria): void {
    foreach ($criteria as $value) {
      Assert::assertStringContainsString((string) $value, $this->driver->getResponseBody());
    }
  }

}
