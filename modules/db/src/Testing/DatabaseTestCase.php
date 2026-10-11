<?php
namespace Starbug\Db\Testing;

use Starbug\Db\Testing\Traits\Database;
use Starbug\Testing\BaseTestCase;

/**
 * Base test case for database-backed tests.
 */
abstract class DatabaseTestCase extends BaseTestCase {

  use Database;

  protected function setUp(): void {
    parent::setUp();
    $this->databaseSetup();
  }
}
