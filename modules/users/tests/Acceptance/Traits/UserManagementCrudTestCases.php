<?php
namespace Starbug\Users\Tests\Acceptance\Traits;

use Starbug\Testing\Traits\DatabaseAssertions;

/**
 * Shared CRUD test cases for user management.
 *
 * Used by both web (Admin) and shell (Developer) acceptance tests.
 * Relies on $this->users being injected as UserManagement.
 */
trait UserManagementCrudTestCases {
  use DatabaseAssertions;

  /**
   * Users can be listed.
   */
  public function testListUsers(): void {
    $this->users->list();
  }

  /**
   * A user can be created successfully.
   */
  public function testCreateUser(): void {
    $user = $this->users->create([
      'first_name' => 'Dave',
      'last_name' => 'Lister',
      'email' => 'dave@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);

    $this->assertEquals('Dave', $user['first_name']);
  }

  /**
   * Creating a user without required fields shows validation errors.
   */
  public function testCreateUserValidation(): void {
    $this->users->create([
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

  /**
   * A user can be edited successfully.
   */
  public function testEditUser(): void {
    $user = $this->users->create([
      'first_name' => 'Dave',
      'last_name' => 'Lister',
      'email' => 'dave@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);

    $this->assertRecordMatches('users', $user['id'], [
      'email' => 'dave@neonrain.com',
      'first_name' => 'Dave',
      'last_name' => 'Lister'
    ]);

    $this->users->update($user['id'], [
      'first_name' => 'David',
      'email' => 'david@neonrain.com'
    ]);

    $this->assertRecordMatches('users', $user['id'], [
      'first_name' => 'David',
      'email' => 'david@neonrain.com',
      'last_name' => 'Lister'
    ]);
  }

  /**
   * A user can be soft-deleted.
   */
  public function testDeleteUser(): void {
    $user = $this->users->create([
      'first_name' => 'Arnold',
      'last_name' => 'Rimmer',
      'email' => 'arnold@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);

    $this->assertRecordNotMatches('users', $user['id'], 'deleted');

    $this->users->delete($user['id']);

    $this->assertRecordMatches('users', $user['id'], 'deleted');
  }
}
