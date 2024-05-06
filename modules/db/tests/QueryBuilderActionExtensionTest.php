<?php
namespace Starbug\Db\Tests;

use Starbug\Db\Query\Extensions\Action;

class QueryBuilderActionExtensionTest extends QueryBuilderTestBase {

  protected $action;

  public function setUp(): void {
    parent::setUp();
    $sessionHandler = new MockSessionHandler();
    $this->action = new Action($sessionHandler, $this->createSchema());
  }

  protected function createQuery() {
    parent::createQuery();
    $this->builder->addExtension("action", $this->action);
    return $this->builder;
  }

  public function testAction() {
    $query = $this->createQuery()->from("users");
    $query->action("read");

    // expected output
    $expected = "SELECT `users`.* ".
                "FROM `test_users` AS `users` ".
                "INNER JOIN `test_permits` AS `permits` ON 'users' LIKE permits.related_table && 'read' LIKE permits.action ".
                "WHERE ".
                  "('global' LIKE `permits`.priv_type || (`permits`.priv_type='object' && `permits`.related_id=`users`.id)) AND ".
                  "(`permits`.object_deleted is null || `permits`.object_deleted=`users`.deleted) AND ".
                  "(`permits`.user_groups IS NULL OR `permits`.user_groups IN (SELECT groups_id FROM `test_users_groups` AS `u` WHERE `u`.users_id = :default0)) AND ".
                  "(`permits`.role='everyone' OR `permits`.role='user' && `permits`.who='2' OR `permits`.role='self' && `users`.id='2' OR `permits`.role='owner' && `users`.owner='2' OR (`permits`.role = :default1 AND (EXISTS (SELECT groups_id FROM `test_users_groups` AS `o` WHERE `o`.users_id=`users`.id AND `o`.groups_id IN (SELECT groups_id FROM `test_users_groups` AS `u` WHERE `u`.users_id = :default2)) OR NOT EXISTS (SELECT groups_id FROM `test_users_groups` AS `o` WHERE `o`.users_id=`users`.id))))";

    // compare
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
    $this->assertSame(2, $this->builder->getQuery()->getParameter("default0"));
    $this->assertSame("groups", $this->builder->getQuery()->getParameter("default1"));
    $this->assertSame(2, $this->builder->getQuery()->getParameter("default2"));
  }
}
