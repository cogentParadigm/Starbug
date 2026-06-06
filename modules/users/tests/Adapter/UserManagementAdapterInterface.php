<?php
namespace Starbug\Users\Tests\Adapter;

/**
 * Contract for user management protocol adapters.
 *
 * Implementations translate CRUD operations into
 * transport-specific calls (HTTP, shell, etc.).
 */
interface UserManagementAdapterInterface {

  /**
   * Create a user.
   *
   * @param array $data Field values.
   */
  public function createUser(array $data): void;

  /**
   * Navigate to or retrieve the users list.
   */
  public function listUsers(): void;

  /**
   * Assert that the last createUser operation succeeded.
   */
  public function assertUserCreated(): void;

  /**
   * Assert that the last createUser operation failed with expected validation errors.
   *
   * @param array $expectedErrors Map of field name => error message fragment.
   */
  public function assertValidationFailed(array $expectedErrors): void;

  /**
   * Assert the current view is the users list.
   */
  public function assertOnUsersList(): void;

  /**
   * Assert a user matching the given criteria exists in the current view.
   *
   * @param array $criteria Values to search for (e.g. ['email' => 'foo@bar.com']).
   */
  public function assertUserExists(array $criteria): void;

}
