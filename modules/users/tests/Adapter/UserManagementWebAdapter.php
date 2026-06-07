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
    $this->driver->submitForm('admin/users/create', $data + $defaults);
  }

  public function listUsers(): void {
    $this->visit('admin/users');
  }

  public function assertUserCreated(): void {
    $this->assertPathEquals('/admin/users');
  }

  public function assertValidationFailed(array $expectedErrors): void {
    $errors = $this->driver->getFormErrors();
    foreach ($expectedErrors as $field => $message) {
      Assert::assertArrayHasKey($field, $errors, "Expected error for field {$field}");
      Assert::assertStringContainsString($message, $errors[$field]);
    }
  }

  public function assertOnUsersList(): void {
    $this->assertPathEquals('/admin/users');
    $this->assertPageContains('New User');
    $this->assertLinkHrefEquals('New User', 'admin/users/create');
  }

  public function assertUserExists(array $criteria): void {
    foreach ($criteria as $value) {
      $this->assertPageContains((string) $value);
    }
  }

  public function fetchUser(int $id): array {
    $this->visit("admin/users/update/{$id}");
    $form = $this->driver->filterOne('form')->form();
    return $form->getValues();
  }

  public function editUser(int $id, array $data): void {
    $this->driver->submitForm("admin/users/update/{$id}", $data);
  }

  public function deleteUser(int $id): void {
    $this->driver->submitForm("admin/users/delete/{$id}", []);
  }

  public function assertUserUpdated(): void {
    $this->assertPathEquals('/admin/users');
  }

  public function assertUserDeleted(): void {
    $this->assertPathEquals('/admin/users');
  }

}
