<?php
namespace Starbug\Core;

use Starbug\Core\Operation\SoftDelete;
use Starbug\Users\Operation\CreateUser;

class UsersTest extends ModelTest {

  public $model = "users";

  public function testCreate() {
    $this->db->remove("users", ["email" => "phpunit@neonrain.com"]);
    $this->operation(CreateUser::class, [
      "first_name" => "PHPUnit",
      "last_name" => "User",
      "email" => "phpunit@neonrain.com",
      "password" => "Mz2&cssMy@9tclan",
      "groups" => "user"
    ]);
    $user = $this->db->query("users")->select("users.*,GROUP_CONCAT(users.groups.slug) as groups")
              ->condition("users.id", $this->db->getInsertId("users"))->condition("users.deleted", "0")->one();
    // lets verify the explicit values were set
    $this->assertEquals($user['email'], "phpunit@neonrain.com");
    // lets also verify that the implicit values were set
    $this->assertEquals($user['groups'], "user");
  }

  public function testDelete() {
    // first assert that the record exists
    $user = $this->db->get("users", ["email" => "phpunit@neonrain.com"], ["limit" => 1]);
    $this->assertEquals(empty($user), false);

    // remove it and assert that the record is gone
    $this->operation(SoftDelete::class, $user);
    $user = $this->db->query("users")->condition("email", "phpunit@neonrain.com")->one();
    $this->assertEquals($user['deleted'], "1");
    $this->db->remove("users_groups", ["users_id" => $user['id']]);
    $this->db->remove("users", ["email" => "phpunit@neonrain.com"]);
  }
}
