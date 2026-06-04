<?php
namespace Starbug\Users\Tests\Acceptance\Developer;

use Starbug\Testing\Attribute\Fixture;
use Starbug\Testing\DatabaseTestCase;
use Starbug\Testing\ShellDriver;
use Starbug\Users\Tests\Adapter\UserManagementShellAdapter;
use Starbug\Users\Tests\Domain\UserManagement;

/**
 * Shell-based acceptance tests for user management.
 *
 * Covers user CRUD operations through the CLI.
 */
#[Fixture('app/fixtures/db/reset.yml')]
class UserManagementTest extends DatabaseTestCase {

  protected UserManagement $users;

  protected function setUp(): void {
    parent::setUp();
    $driver = new ShellDriver();
    $adapter = new UserManagementShellAdapter($driver);
    $this->users = new UserManagement($adapter);
  }

  /**
   * Users can be listed through the shell query command.
   */
  public function testListUsers(): void {
    $this->users->listUsers();

    $this->users->assertOnUsersList();
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

    $this->users->assertUserCreated();
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
    ]);

    $this->users->assertValidationFailed([
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required',
      'password' => 'required'
    ]);
  }
}
