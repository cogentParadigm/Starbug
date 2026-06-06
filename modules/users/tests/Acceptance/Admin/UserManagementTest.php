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
    $this->users->listUsers();

    $this->users->assertOnUsersList();
    $this->assertPageContains('New User');
  }

  /**
   * An admin can create a new user successfully.
   */
  public function testCreateUser(): void {
    $this->users->createUser([
      'first_name' => 'Dave',
      'last_name' => 'Lister',
      'email' => 'dave@neonrain.com',
      'password' => 'Wj6tNdQP3bnc22IQ'
    ]);

    $this->users->assertUserCreated();
    $this->users->assertOnUsersList();
  }

  /**
   * Creating a user without required fields shows validation errors.
   */
  public function testCreateUserValidation(): void {
    $this->users->createUser([]);

    $this->users->assertValidationFailed([
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required',
      'password' => 'required'
    ]);
  }

  /**
   * An admin can navigate to the create user page from the users list.
   */
  public function testNavigateToCreateUser(): void {
    $this->getDriver()->get('/admin/users');
    $this->getDriver()->followLink('New User');
    $this->assertPathEquals('/admin/users/create');
  }

  /**
   * The users list shows a paged grid with the expected columns.
   */
  public function testListUsersGridColumns(): void {
    $this->users->listUsers();

    $headers = $this->getDriver()->filter('table[data-dojo-type="starbug/grid/PagedGrid"] th');
    $actual = [];
    foreach ($headers as $node) {
      $actual[] = trim($node->textContent);
    }
    foreach (['First Name', 'Last Name', 'Email', 'Last Visit', 'Groups', 'Status', 'Options'] as $column) {
      $this->assertContains($column, $actual, "Expected column '{$column}' not found in grid.");
    }
  }

  /**
   * Users can be exported as CSV.
   */
  public function testExportUsersCsv(): void {
    $csv = $this->getDriver()->download('/api/users/admin.csv');

    $this->assertStringContainsString('First Name', $csv);
    $this->assertStringContainsString('Last Name', $csv);
    $this->assertStringContainsString('Email', $csv);
  }
}
