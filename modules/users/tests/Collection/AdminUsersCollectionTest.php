<?php
namespace Starbug\Users\Tests\Collection;

use DI\Attribute\Inject;
use Starbug\Db\CollectionInterface;
use Starbug\Db\Testing\DatabaseTestCase;
use Starbug\Db\Testing\Traits\CollectionAssertions;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Users\Collection\AdminUsersCollection;

/**
 * Database integration tests for the admin users collection.
 *
 * Seeds users with different groups and deleted status, then validates
 * the collection results for grouping, filtering, and default sorting.
 *
 * Assertions use `assertQueryContains` (partial, in-order match) so tests
 * can focus on the fields the collection contributes (status, groups)
 * rather than the full user row the join produces. The empty-result
 * test uses strict `assertQuery([], ...)` since both sides are `[]`.
 *
 * Note: when filtered by a single group, the collection's GROUP_CONCAT
 * only concatenates the filtered group(s), not all of the user's groups.
 * So the unfiltered `groups` values differ from the filtered ones.
 */
#[Fixture('modules/users/tests/fixtures/db/admin_users_collection.yml')]
class AdminUsersCollectionTest extends DatabaseTestCase {
  use CollectionAssertions;

  #[Inject]
  protected AdminUsersCollection $collection;

  public function testReturnsAllActiveUsersByDefault(): void {
    $this->assertQueryContains([
      ['email' => 'another@example.com', 'deleted' => 'Active'],
      ['email' => 'regular@example.com', 'deleted' => 'Active'],
      ['email' => 'root', 'deleted' => 'Active'],
    ]);
  }

  public function testFiltersBySingleGroup(): void {
    $this->assertQueryContains([
      ['email' => 'another@example.com', 'deleted' => 'Active'],
      ['email' => 'root', 'deleted' => 'Active'],
    ], ['group' => $this->groupId('admin')]);
  }

  public function testFiltersByMultipleGroups(): void {
    $this->assertQueryContains([
      ['email' => 'another@example.com', 'deleted' => 'Active'],
      ['email' => 'regular@example.com', 'deleted' => 'Active'],
      ['email' => 'root', 'deleted' => 'Active'],
    ], ['groups' => $this->groupId('user') . ',' . $this->groupId('admin')]);
  }

  public function testIncludesDeletedUsersWhenRequested(): void {
    $this->assertQueryContains([
      ['email' => 'deleted@example.com', 'deleted' => 'Deleted'],
    ], ['deleted' => '1']);
  }

  public function testDefaultSortIsByLastVisitDescending(): void {
    $this->assertSame(
      ['another@example.com', 'regular@example.com', 'root'],
      array_column($this->query(), 'email')
    );
  }

  public function testReturnsNoResultsForUnusedGroup(): void {
    $this->assertQuery([], ['group' => '999999']);
  }

  public function testCombinesGroupAndDeletedFilters(): void {
    $this->assertQueryContains([
      ['email' => 'deleted@example.com', 'deleted' => 'Deleted'],
    ], ['group' => $this->groupId('user'), 'deleted' => '1']);
  }

  public function testRespectsCustomSortOrder(): void {
    $this->assertSame(
      ['another@example.com', 'regular@example.com', 'root'],
      array_column($this->query(['sort' => 'email ASC']), 'email')
    );
  }

  private function groupId(string $slug): int {
    return (int) $this->assertRecordExists('groups', ['slug' => $slug])['id'];
  }
}
