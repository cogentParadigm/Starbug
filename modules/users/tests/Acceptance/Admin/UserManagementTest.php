<?php
namespace Starbug\Users\Tests\Acceptance\Admin;

use DI\Attribute\Inject;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Testing\WebTestCase;
use Starbug\Users\Tests\Domain\Authentication;
use Starbug\Users\Tests\Domain\UserManagement;

/**
 * Web-based acceptance tests for user management.
 *
 * Covers user CRUD operations through the admin web UI.
 */
#[Fixture('app/fixtures/db/reset.yml')]
class UserManagementTest extends WebTestCase {

  #[Inject]
  protected Authentication $auth;

  #[Inject]
  protected UserManagement $users;

  protected function setUp(): void {
    parent::setUp();
    $this->auth->login('root', 'Mz2&cssMy@9tclan');
  }

  /**
   * An admin can view the users list.
   */
  public function testListUsers(): void {
    $this->users->list();
  }

  /**
   * An admin can create a new user successfully.
   */
  public function testCreateUser(): void {
    $this->users->create([
      'first_name' => 'Dave',
      'last_name' => 'Lister',
      'email' => 'dave@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);

    $user = $this->assertRecordExists('users', ['email' => 'dave@neonrain.com']);
    $this->assertEquals('Dave', $user['first_name']);
  }

  /**
   * Creating a user without required fields shows validation errors.
   */
  public function testCreateUserValidation(): void {
    $this->users->create([], false);

    $this->users->assertValidationFailed([
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required',
      'password' => 'required'
    ]);
  }

  /**
   * Users can be exported as CSV.
   */
  public function testExportUsersCsv(): void {
    $this->assertTextDownloadContains('api/users/admin.csv', ['First Name', 'Last Name', 'Email']);
  }

  /**
   * An admin can edit an existing user successfully.
   */
  public function testEditUser(): void {
    $this->users->create([
      'first_name' => 'Dave',
      'last_name' => 'Lister',
      'email' => 'dave@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);

    $user = $this->assertRecordExists('users', ['email' => 'dave@neonrain.com']);

    $initial = $this->users->read($user['id']);
    $this->assertEquals('dave@neonrain.com', $initial['email']);
    $this->assertEquals('Dave', $initial['first_name']);
    $this->assertEquals('Lister', $initial['last_name']);

    $this->users->update($user['id'], [
      'first_name' => 'David',
      'email' => 'david@neonrain.com'
    ]);

    $updated = $this->users->read($user['id']);
    $this->assertEquals('David', $updated['first_name']);
    $this->assertEquals('david@neonrain.com', $updated['email']);
    $this->assertEquals('Lister', $updated['last_name']);
  }

  /**
   * An admin can soft-delete a user.
   */
  public function testDeleteUser(): void {
    $this->users->create([
      'first_name' => 'Arnold',
      'last_name' => 'Rimmer',
      'email' => 'arnold@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);

    $user = $this->assertRecordExists('users', ['email' => 'arnold@neonrain.com']);

    $before = $this->users->read($user['id']);
    $this->assertEquals('0', $before['deleted']);

    $this->users->delete($user['id']);

    $after = $this->users->read($user['id']);
    $this->assertEquals('1', $after['deleted']);
  }
}
