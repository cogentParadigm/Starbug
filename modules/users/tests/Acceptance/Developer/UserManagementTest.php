<?php
namespace Starbug\Users\Tests\Acceptance\Developer;

use DI\Attribute\Inject;
use Starbug\Testing\Attribute\Bind;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Testing\DatabaseTestCase;
use Starbug\Users\Tests\Adapter\UserManagementAdapterInterface;
use Starbug\Users\Tests\Adapter\UserManagementShellAdapter;
use Starbug\Users\Tests\Domain\UserManagement;

/**
 * Shell-based acceptance tests for user management.
 *
 * Covers user CRUD operations through the CLI.
 */
#[Bind(UserManagementAdapterInterface::class, UserManagementShellAdapter::class)]
#[Fixture('app/fixtures/db/reset.yml')]
class UserManagementTest extends DatabaseTestCase {

  #[Inject]
  protected UserManagement $users;

  /**
   * Users can be listed through the shell query command.
   */
  public function testListUsers(): void {
    $this->users->listUsers();
  }

  /**
   * A user can be created successfully through the shell store command.
   */
  public function testCreateUser(): void {
    $this->users->createUser([
      'first_name' => 'Dave',
      'last_name' => 'Lister',
      'email' => 'dave@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);
  }

  /**
   * Creating a user without required fields shows validation errors.
   */
  public function testCreateUserValidation(): void {
    $this->users->createUser([
      'first_name' => '',
      'last_name' => '',
      'email' => '',
      'password' => ''
    ], false);

    $this->users->assertValidationFailed([
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required',
      'password' => 'required'
    ]);
  }
}
