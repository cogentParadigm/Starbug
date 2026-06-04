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
   */
  public function createUser(array $data): void {
    $defaults = [
      'password_confirm' => $data['password'] ?? ''
    ];
    $this->adapter->createUser($data + $defaults);
  }

  /**
   * Navigate to or retrieve the users list.
   */
  public function listUsers(): void {
    $this->adapter->listUsers();
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
   * Navigate to the create user page.
   */
  public function navigateToCreateUser(): void {
    $this->adapter->navigateToCreateUser();
  }

  /**
   * Assert the current view is the create user page.
   */
  public function assertOnCreateUserPage(): void {
    $this->adapter->assertOnCreateUserPage();
  }

  /**
   * Export users CSV and return the content.
   *
   * @return string The CSV response body.
   */
  public function exportUsersCsv(): string {
    return $this->adapter->exportUsersCsv();
  }

  /**
   * Assert the paged grid on the current page has the expected columns.
   *
   * @param array $columns List of expected column header texts.
   */
  public function assertPagedGridHasColumns(array $columns): void {
    $this->adapter->assertPagedGridHasColumns($columns);
  }
}
