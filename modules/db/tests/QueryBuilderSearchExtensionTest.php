<?php
namespace Starbug\Db\Tests;

use Starbug\Db\Query\Extensions\Search;

class QueryBuilderSearchExtensionTest extends QueryBuilderTestBase {

  public function setUp(): void {
    parent::setUp();
    $this->search = new Search($this->createSchema());
  }

  protected function createQuery() {
    parent::createQuery();
    $this->builder->addExtension("search", $this->search);
    return $this->builder;
  }

  public function testSearchAnd() {
    $query = $this->createQuery()->from("users");
    $query->search("ali gangji", "first_name,last_name");

    // expected output
    $expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE ((first_name LIKE '%ali%' OR last_name LIKE '%ali%') AND (first_name LIKE '%gangji%' OR last_name LIKE '%gangji%'))";

    // compare
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  public function testSearchOr() {
    $query = $this->createQuery()->from("users");
    $query->search("ali or gangji", "first_name,last_name");

    // expected output
    $expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE ((first_name LIKE '%ali%' OR last_name LIKE '%ali%') or (first_name LIKE '%gangji%' OR last_name LIKE '%gangji%'))";

    // compare
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

  public function testSearchFields() {
    $query = $this->createQuery()->from("users");
    $query->search("ali", "first_name");
    $query->search("gangji", "last_name");

    // expected output
    $expected = "SELECT `users`.* FROM `test_users` AS `users` WHERE ((first_name LIKE '%ali%')) AND ((last_name LIKE '%gangji%'))";

    // compare
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }
}
