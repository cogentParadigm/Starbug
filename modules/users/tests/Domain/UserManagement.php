<?php
namespace Starbug\Users\Tests\Domain;

use Starbug\Users\Tests\Adapter\UserManagementAdapterInterface;

/**
 * Domain interaction for user management.
 *
 * Composes a UserManagementAdapterInterface into test-friendly
 * operations with sensible defaults and built-in assertions.
 */
class UserManagement {

  public function __construct(
    private UserManagementAdapterInterface $adapter
  ) {
  }

  /**
   * Create a user with sensible defaults.
   *
   * @param bool $expectSuccess Whether to assert the operation succeeded.
   */
  public function createUser(array $data, bool $expectSuccess = true): void {
    $defaults = [
      'password_confirm' => $data['password'] ?? ''
    ];
    $this->adapter->createUser($data + $defaults);
    if ($expectSuccess) {
      $this->adapter->assertUserCreated();
    }
  }

  /**
   * Navigate to or retrieve the users list.
   */
  public function listUsers(): void {
    $this->adapter->listUsers();
    $this->assertOnUsersList();
  }

  /**
   * Assert the last createUser operation succeeded.
   */
  public function assertUserCreated(): void {
    $this->adapter->assertUserCreated();
  }

  /**
   * Assert the last createUser operation failed with expected validation errors.
   *
   * @param array $expectedErrors Map of field name => error message fragment.
   */
  public function assertValidationFailed(array $expectedErrors): void {
    $this->adapter->assertValidationFailed($expectedErrors);
  }

  /**
   * Assert the current view is the users list.
   */
  public function assertOnUsersList(): void {
    $this->adapter->assertOnUsersList();
  }

  /**
   * Assert a user matching the given criteria exists.
   *
   * @param array $criteria Values to search for.
   */
  public function assertUserExists(array $criteria): void {
    $this->adapter->assertUserExists($criteria);
  }

  /**
   * Retrieve a single user's data by ID.
   *
   * @return array Associative array of field values.
   */
  public function fetchUser(int $id): array {
    return $this->adapter->fetchUser($id);
  }

  /**
   * Edit a user, passing only the fields to change.
   *
   * The adapter's submitForm preserves all untouched form values.
   *
   * @param bool $expectSuccess Whether to assert the operation succeeded.
   */
  public function editUser(int $id, array $data, bool $expectSuccess = true): void {
    if (isset($data['password'])) {
      $data['password_confirm'] = $data['password'];
    }
    $this->adapter->editUser($id, $data);
    if ($expectSuccess) {
      $this->adapter->assertUserUpdated();
    }
  }

  /**
   * Delete a user.
   *
   * @param bool $expectSuccess Whether to assert the operation succeeded.
   */
  public function deleteUser(int $id, bool $expectSuccess = true): void {
    $this->adapter->deleteUser($id);
    if ($expectSuccess) {
      $this->adapter->assertUserDeleted();
    }
  }

  /**
   * Assert the last editUser operation succeeded.
   */
  public function assertUserUpdated(): void {
    $this->adapter->assertUserUpdated();
  }

  /**
   * Assert the last deleteUser operation succeeded.
   */
  public function assertUserDeleted(): void {
    $this->adapter->assertUserDeleted();
  }

}
