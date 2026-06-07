<?php
namespace Starbug\Users\Tests\Adapter;

use PHPUnit\Framework\Assert;
use Starbug\Testing\ShellAdapter;

/**
 * Shell-based protocol adapter for user management.
 *
 * Translates user CRUD operations into CLI commands
 * via the underlying driver.
 */
class UserManagementShellAdapter extends ShellAdapter implements UserManagementAdapterInterface {

  protected string $lastOutput = '';

  public function createUser(array $data): void {
    $argv = ['php', 'sb', 'store', 'users', '-t'];
    foreach ($data as $key => $value) {
      if ($key === 'password_confirm') {
        continue;
      }
      $argv[] = "{$key}:{$value}";
    }
    $result = $this->driver->run($argv);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function listUsers(): void {
    $result = $this->driver->run(['php', 'sb', 'query', 'users', '-t']);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function assertUserCreated(): void {
    $this->assertNoErrorsInOutput();
  }

  public function assertValidationFailed(array $expectedErrors): void {
    foreach ($expectedErrors as $field => $message) {
      Assert::assertStringContainsString($field, $this->lastOutput, "Expected error for field '{$field}' in output");
      Assert::assertStringContainsString($message, $this->lastOutput, "Expected error message '{$message}' in output");
    }
  }

  public function assertOnUsersList(): void {
    Assert::assertNotEmpty($this->lastOutput, 'Expected output from query command');
  }

  public function assertUserExists(array $criteria): void {
    foreach ($criteria as $value) {
      Assert::assertStringContainsString((string) $value, $this->lastOutput, "Expected value '{$value}' in output");
    }
  }

  public function fetchUser(int $id): array {
    $result = $this->driver->run(['php', 'sb', 'query', 'users', '-t', "where:id={$id}"]);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
    return $this->parseTabularOutput($this->lastOutput);
  }

  public function editUser(int $id, array $data): void {
    $argv = ['php', 'sb', 'store', 'users', '-t'];
    $argv[] = "id:{$id}";
    foreach ($data as $key => $value) {
      if ($key === 'password_confirm') {
        continue;
      }
      $argv[] = "{$key}:{$value}";
    }
    $result = $this->driver->run($argv);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function deleteUser(int $id): void {
    $result = $this->driver->run(['php', 'sb', 'store', 'users', '-t', "id:{$id}", 'deleted:1']);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function assertUserUpdated(): void {
    $this->assertNoErrorsInOutput();
  }

  public function assertUserDeleted(): void {
    $this->assertNoErrorsInOutput();
  }

  /**
   * Assert that the last operation produced no error table in CLI output.
   */
  protected function assertNoErrorsInOutput(): void {
    Assert::assertStringNotContainsString('field', $this->lastOutput, 'Expected operation to succeed, but found error table in output');
    Assert::assertStringNotContainsString('message', $this->lastOutput, 'Expected operation to succeed, but found error table in output');
    Assert::assertNotEmpty($this->lastOutput, 'Expected output from command');
  }

  /**
   * Parse tab-separated tabular CLI output into an associative array.
   *
   * Expects the last two non-empty lines to be the header row and the data row.
   */
  protected function parseTabularOutput(string $output): array {
    $lines = array_filter(array_map('trim', explode("\n", trim($output))));

    if (count($lines) < 2) {
      return [];
    }

    $headers = str_getcsv(array_pop($lines), "\t");
    $values  = str_getcsv(array_pop($lines), "\t");

    return array_combine($headers, $values);
  }

}
