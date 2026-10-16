<?php
namespace Starbug\Users\Tests\Domain;

use Starbug\Testing\Domain\Crud;
use Starbug\Users\Tests\Adapter\UserManagementAdapterInterface;

/**
 * Domain interaction for user management.
 *
 * Extends the generic Crud domain with user-specific defaults
 * (e.g. auto-filling password_confirm).
 */
class UserManagement extends Crud {

  public function __construct(
    UserManagementAdapterInterface $adapter
  ) {
    parent::__construct($adapter);
  }

  /**
   * Create a user with sensible defaults.
   *
   * @param  bool $expectSuccess Whether to assert the operation succeeded.
   * @return array The created record if expectSuccess is true, otherwise empty.
   */
  public function create(array $data, bool $expectSuccess = true): array {
    $defaults = [
      'password_confirm' => $data['password'] ?? ''
    ];
    return parent::create($data + $defaults, $expectSuccess);
  }

  /**
   * Edit a user, passing only the fields to change.
   *
   * The adapter's submitForm preserves all untouched form values.
   *
   * @param  bool $expectSuccess Whether to assert the operation succeeded.
   * @return array The updated record if expectSuccess is true, otherwise empty.
   */
  public function update(int $id, array $data, bool $expectSuccess = true): array {
    if (isset($data['password'])) {
      $data['password_confirm'] = $data['password'];
    }
    return parent::update($id, $data, $expectSuccess);
  }
}
