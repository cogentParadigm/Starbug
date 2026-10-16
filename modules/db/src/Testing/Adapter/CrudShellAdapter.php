<?php
namespace Starbug\Db\Testing\Adapter;

use PHPUnit\Framework\Assert;
use Starbug\Db\DatabaseInterface;
use Starbug\Db\Testing\Traits\DatabaseAssertions;
use Starbug\Testing\Adapter\CrudAdapterInterface;
use Starbug\Testing\ShellAdapter;
use Starbug\Testing\ShellDriverInterface;

/**
 * Shell-based CRUD adapter base class.
 *
 * Parameterize via $entity and the standard CRUD operations
 * work out of the box using the sb store/query commands.
 */
abstract class CrudShellAdapter extends ShellAdapter implements CrudAdapterInterface {
  use DatabaseAssertions;

  protected string $entity = '';
  protected string $lastOutput = '';
  protected ?int $lastUpdatedId = null;

  public function __construct(
    ShellDriverInterface $driver,
    protected DatabaseInterface $db
  ) {
    parent::__construct($driver);
  }

  public function create(array $data): void {
    $argv = ['php', 'sb', 'store', $this->entity, '-t'];
    foreach ($data as $key => $value) {
      $argv[] = "{$key}:{$value}";
    }
    $result = $this->driver->run($argv);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function read(int $id): array {
    $result = $this->driver->run(['php', 'sb', 'query', $this->entity, '-t', "where:id={$id}"]);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
    return $this->parseTabularOutput($this->lastOutput);
  }

  public function update(int $id, array $data): void {
    $this->lastUpdatedId = $id;
    $argv = ['php', 'sb', 'store', $this->entity, '-t'];
    $argv[] = "id:{$id}";
    foreach ($data as $key => $value) {
      $argv[] = "{$key}:{$value}";
    }
    $result = $this->driver->run($argv);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function delete(int $id): void {
    $result = $this->driver->run(['php', 'sb', 'store', $this->entity, '-t', "id:{$id}", 'deleted:1']);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function list(): void {
    $result = $this->driver->run(['php', 'sb', 'query', $this->entity, '-t']);
    $this->lastOutput = $result['stdout'] . $result['stderr'];
  }

  public function assertCreated(): array {
    $this->assertNoErrorsInOutput();
    $record = $this->parseTabularOutput($this->lastOutput);
    Assert::assertNotEmpty($record, 'Expected created record in tabular output');
    return $record;
  }

  public function assertRead(int $id): void {
    $this->assertNoErrorsInOutput();
  }

  public function assertUpdated(): array {
    $this->assertNoErrorsInOutput();
    Assert::assertNotNull($this->lastUpdatedId, 'No update has been performed.');
    return $this->assertRecordExists($this->entity, ['id' => $this->lastUpdatedId]);
  }

  public function assertDeleted(): void {
    $this->assertNoErrorsInOutput();
  }

  public function assertOnList(): void {
    Assert::assertNotEmpty($this->lastOutput, 'Expected output from query command');
  }

  public function assertValidationFailed(array $expectedErrors): void {
    foreach ($expectedErrors as $field => $message) {
      Assert::assertStringContainsString($field, $this->lastOutput, "Expected error for field '{$field}' in output");
      Assert::assertStringContainsString($message, $this->lastOutput, "Expected error message '{$message}' in output");
    }
  }

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

    $values  = str_getcsv(array_pop($lines), "\t");
    $headers = str_getcsv(array_pop($lines), "\t");

    return array_combine($headers, $values);
  }

  protected function getDatabase(): DatabaseInterface {
    return $this->db;
  }
}
