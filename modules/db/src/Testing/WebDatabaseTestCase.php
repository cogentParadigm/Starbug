<?php
namespace Starbug\Db\Testing;

use Starbug\Db\Testing\Traits\Database;
use Starbug\Testing\WebTestCase;

/**
 * Web acceptance test case with database-backed fixtures.
 *
 * Combines the in-process HTTP driver from `Starbug\Testing\WebTestCase`
 * with the database fixture loading and error reset from the `Database`
 * trait, which `DatabaseTestCase` also applies for non-web tests.
 */
abstract class WebDatabaseTestCase extends WebTestCase {

  use Database;

  protected function setUp(): void {
    parent::setUp();
    $this->databaseSetup();
  }
}
