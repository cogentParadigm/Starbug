<?php
namespace Starbug\Db\Testing\Traits;

use PHPUnit\Framework\Assert;

/**
 * Database assertion helpers for PHPUnit tests.
 *
 * These methods wrap common database query-and-assert patterns. The
 * `Database` trait is responsible for providing `getDatabase()`.
 */
trait DatabaseAssertions {

  /**
   * Create a database record directly via db->store().
   *
   * @param string $entity The table/model name.
   * @param array $data Column-value pairs.
   *
   * @return array The stored record.
   */
  protected function createRecord(string $entity, array $data): array {
    $db = $this->getDatabase();
    $db->store($entity, $data);
    $id = $db->getInsertId($entity);
    $record = $db->query($entity)->condition("id", $id)->one();
    Assert::assertNotEmpty($record, "Failed to create {$entity} record.");
    return $record;
  }

  /**
   * Assert a record was created by the last insert operation.
   *
   * @param string $entity The table/model name.
   * @param string|null $message Optional assertion message.
   *
   * @return array The created record.
   */
  protected function assertRecordCreated(string $entity, ?string $message = null): array {
    $id = $this->getDatabase()->getInsertId($entity);
    return $this->assertRecordExists($entity, ['id' => $id], $message ?? "Expected a newly created {$entity} record.");
  }

  /**
   * Assert a record exists matching the given conditions.
   *
   * @param string $entity The table/model name.
   * @param array $conditions Column-value conditions.
   * @param string|null $message Optional assertion message.
   *
   * @return array The matching record.
   */
  protected function assertRecordExists(string $entity, array $conditions, ?string $message = null): array {
    $record = $this->getDatabase()->query($entity)->conditions($conditions)->one();
    Assert::assertNotEmpty($record, $message ?? "Expected a {$entity} record matching " . json_encode($conditions) . ".");
    return $record;
  }

  /**
   * Assert no record exists matching the given conditions.
   *
   * @param string $entity The table/model name.
   * @param array $conditions Column-value conditions.
   * @param string|null $message Optional assertion message.
   */
  protected function assertRecordNotExists(string $entity, array $conditions, ?string $message = null): void {
    $record = $this->getDatabase()->query($entity)->conditions($conditions)->one();
    Assert::assertEmpty($record, $message ?? "Did not expect a {$entity} record matching " . json_encode($conditions) . ".");
  }

  /**
   * Assert the number of records matching conditions.
   *
   * @param int $expected Expected count.
   * @param string $entity The table/model name.
   * @param array $conditions Column-value conditions.
   * @param string|null $message Optional assertion message.
   */
  protected function assertRecordCount(int $expected, string $entity, array $conditions = [], ?string $message = null): void {
    $query = $this->getDatabase()->query($entity);
    if (!empty($conditions)) {
      $query->conditions($conditions);
    }
    $count = $query->count();
    Assert::assertSame($expected, $count, $message ?? "Expected {$expected} {$entity} records, found {$count}.");
  }

  /**
   * Assert a field value contains a substring (LIKE match).
   *
   * @param string $entity The table/model name.
   * @param string $field The column to search.
   * @param string $value The substring to match.
   * @param array $conditions Additional column-value conditions.
   * @param string|null $message Optional assertion message.
   *
   * @return array The matching record.
   */
  protected function assertFieldValueContains(string $entity, string $field, string $value, array $conditions = [], ?string $message = null): array {
    $query = $this->getDatabase()->query($entity);
    if (!empty($conditions)) {
      $query->conditions($conditions);
    }
    $record = $query->where("{$field} LIKE \"%" . $value . "%\"")->one();
    Assert::assertNotEmpty($record, $message ?? "Expected a {$entity} record where {$field} contains '{$value}'.");
    return $record;
  }

  /**
   * Assert a record matches expected field values.
   *
   * @param string $entity The table/model name.
   * @param array|int|string $locator Column-value conditions to locate the record, or a single ID.
   * @param array|string $expected Map of field name => expected value, or a single flag field name asserting '1'.
   * @param string|null $message Optional assertion message.
   *
   * @return array The matching record.
   */
  protected function assertRecordMatches(string $entity, array|int|string $locator, array|string $expected, ?string $message = null): array {
    $conditions = is_array($locator) ? $locator : ['id' => $locator];
    $record = $this->assertRecordExists($entity, $conditions, $message);

    if (is_string($expected)) {
      $expected = [$expected => '1'];
    }

    foreach ($expected as $field => $value) {
      Assert::assertArrayHasKey($field, $record, "Record missing expected field '{$field}'.");
      Assert::assertEquals($value, $record[$field], $message ?? "Expected {$entity}.{$field} to equal '{$value}', got '{$record[$field]}'.");
    }
    return $record;
  }

  /**
   * Assert a record does NOT match expected field values.
   *
   * When $expected is an array, at least one field must differ (Option B).
   * When $expected is a string flag, asserts the flag is '0'.
   *
   * @param string $entity The table/model name.
   * @param array|int|string $locator Column-value conditions to locate the record, or a single ID.
   * @param array|string $expected Map of field name => expected value, or a single flag field name asserting '0'.
   * @param string|null $message Optional assertion message.
   *
   * @return array The matching record.
   */
  protected function assertRecordNotMatches(string $entity, array|int|string $locator, array|string $expected, ?string $message = null): array {
    $conditions = is_array($locator) ? $locator : ['id' => $locator];
    $record = $this->assertRecordExists($entity, $conditions, $message);

    if (is_string($expected)) {
      Assert::assertArrayHasKey($expected, $record, "Record missing expected field '{$expected}'.");
      Assert::assertEquals('0', $record[$expected], $message ?? "Expected {$entity}.{$expected} to equal '0', got '{$record[$expected]}'.");
      return $record;
    }

    $mismatchFound = false;
    foreach ($expected as $field => $value) {
      Assert::assertArrayHasKey($field, $record, "Record missing expected field '{$field}'.");
      if ($record[$field] !== $value) {
        $mismatchFound = true;
      }
    }
    Assert::assertTrue($mismatchFound, $message ?? "Expected {$entity} record to differ from " . json_encode($expected) . ", but all fields matched.");
    return $record;
  }

  /**
   * Assert the database has expected validation errors.
   *
   * @param array $expectedErrors Map of field name => error message fragment.
   * @param string|null $message Optional assertion message.
   */
  protected function assertDatabaseErrors(array $expectedErrors, ?string $message = null): void {
    $errors = $this->getDatabase()->errors(true);
    foreach ($expectedErrors as $field => $errorFragment) {
      $found = false;
      foreach ($errors as $scope => $scopeErrors) {
        if (isset($scopeErrors[$field])) {
          $messages = is_array($scopeErrors[$field]) ? $scopeErrors[$field] : [$scopeErrors[$field]];
          foreach ($messages as $msg) {
            if (str_contains($msg, $errorFragment)) {
              $found = true;
              break 2;
            }
          }
        }
      }
      Assert::assertTrue($found, $message ?? "Expected error for field '{$field}' to contain '{$errorFragment}'.");
    }
  }
}
