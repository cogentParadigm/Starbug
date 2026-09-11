<?php
namespace Starbug\Db\Testing\Traits;

use PHPUnit\Framework\Assert;
use Starbug\Db\CollectionInterface;

/**
 * Collection query and assertion helpers for PHPUnit tests.
 *
 * The test class declares a `$collection` property naming the collection to
 * exercise, then uses `query()`/`one()` to fetch results and `assertQuery()`
 * variants to verify them. The trait owns no container state of its own;
 * `CollectionFactoryInterface` is injected by the test container.
 */
trait CollectionAssertions {

  protected function getCollection(): CollectionInterface {
    return $this->collection;
  }

  /**
   * Query the collection.
   *
   * @param array $ops Query options.
   *
   * @return array The collection result rows.
   */
  protected function query(array $ops = []): array {
    return $this->getCollection()->query($ops);
  }

  /**
   * Query the collection for a single result row.
   *
   * @param array $ops Query options.
   *
   * @return array The result row.
   */
  protected function one(array $ops = []): array {
    return $this->getCollection()->one($ops);
  }

  /**
   * Assert the full result set matches the expected rows in order.
   *
   * @param array $expected Expected rows in expected order.
   * @param array $ops Query options.
   * @param string|null $message Optional failure message.
   */
  protected function assertQuery(array $expected, array $ops = [], ?string $message = null): void {
    Assert::assertSame($expected, $this->query($ops), $message ?? "Collection result did not match.");
  }

  /**
   * Assert each expected row appears in the result set, in order.
   *
   * Other rows in the result set are allowed. Each expected row is
   * matched as a field-subset: every field in the expected row must
   * equal the corresponding field in some result row, in document
   * order (cursor advances after each match).
   *
   * @param array $expected Expected rows in expected order.
   * @param array $ops Query options.
   * @param string|null $message Optional failure message.
   */
  protected function assertQueryContains(array $expected, array $ops = [], ?string $message = null): void {
    $actual = $this->query($ops);
    Assert::assertNotEmpty($expected, "assertQueryContains requires at least one expected row.");
    $cursor = 0;
    foreach ($expected as $row) {
      $found = false;
      for ($i = $cursor, $n = count($actual); $i < $n; $i++) {
        if ($this->rowMatchesSubset($row, $actual[$i])) {
          $cursor = $i + 1;
          $found = true;
          break;
        }
      }
      Assert::assertTrue($found, ($message ?? "Expected to find row in result set") . ": " . json_encode($row));
    }
  }

  /**
   * Assert none of the expected rows appear in the result set.
   *
   * Each expected row is matched as a field-subset (same semantics as
   * assertQueryContains()): every field in the expected row must
   * match for the row to be considered "contained".
   *
   * @param array $expected Rows that must not appear.
   * @param array $ops Query options.
   * @param string|null $message Optional failure message.
   */
  protected function assertQueryNotContains(array $expected, array $ops = [], ?string $message = null): void {
    $actual = $this->query($ops);
    Assert::assertNotEmpty($expected, "assertQueryNotContains requires at least one expected row.");
    foreach ($expected as $row) {
      $found = false;
      foreach ($actual as $candidate) {
        if ($this->rowMatchesSubset($row, $candidate)) {
          $found = true;
          break;
        }
      }
      Assert::assertFalse($found, ($message ?? "Did not expect to find row in result set") . ": " . json_encode($row));
    }
  }

  /**
   * Check that $expected's fields all match $actual's corresponding fields.
   *
   * Extra fields in $actual are allowed. Missing fields in $actual fail.
   * Values are compared strictly.
   */
  private function rowMatchesSubset(array $expected, array $actual): bool {
    foreach ($expected as $field => $value) {
      if (!array_key_exists($field, $actual) || $actual[$field] !== $value) {
        return false;
      }
    }
    return true;
  }
}
