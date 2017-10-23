<?php
namespace Starbug\Db\Tests;
use Starbug\Core\Identity;
use Starbug\Db\Query\Extensions\Action;
class QueryBuilderActionExtensionTest extends QueryBuilderTestBase {

  function setUp() {
    parent::setUp();
    $this->user = new Identity();
    $this->action = new Action($this->user, $this->createSchema());
  }

  function createQuery() {
    parent::createQuery();
    $this->builder->addExtension("action", $this->action);
    return $this->builder;
  }

  function testAction() {
    $query = $this->createQuery()->from("users");
    $this->user->setUser(array("id" => 2));
    $query->action("read");

    //expected output
    $expected = "SELECT `users`.* ".
                "FROM `test_users` AS `users` ".
                "INNER JOIN `test_permits` AS `permits` ON 'users' LIKE permits.related_table && 'read' LIKE permits.action ".
                "WHERE ".
                  "('global' LIKE `permits`.priv_type || (`permits`.priv_type='object' && `permits`.related_id=`users`.id)) && ".
                  "(`permits`.object_deleted is null || `permits`.object_deleted=`users`.deleted) && ".
                  "(`permits`.user_groups is null || `permits`.user_groups IN (SELECT groups_id FROM `users_groups` u WHERE `u`.users_id=2)) && ".
                  "(`permits`.role='everyone' || `permits`.role='user' && `permits`.who='2' || `permits`.role='self' && `users`.id='2' || `permits`.role='owner' && `users`.owner='2' || `permits`.role='groups' && (EXISTS (SELECT groups_id FROM `users_groups` o WHERE `o`.users_id=`users`.id && `o`.groups_id IN (SELECT groups_id FROM `users_groups` u WHERE `u`.users_id=2)) || NOT EXISTS (SELECT groups_id FROM `users_groups` o WHERE `o`.users_id=`users`.id)))";

    //compare
    $actual = $this->compile();
    $this->assertSame($expected, $actual);
  }

}
