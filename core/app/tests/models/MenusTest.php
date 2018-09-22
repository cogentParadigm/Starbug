<?php
namespace Starbug\Core;

class MenusTest extends ModelTest {

  public $model = "menus";

  public function testCreateUpdateDelete() {
    // create
    $this->action("create", ["content" => "PHP Unit", "menu" => "phpunit"]);
    $id = $this->insert_id;
    $object = $this->db->query($this->model)->condition("id", $id)->one();
    // lets verify the explicit values were set
    $this->assertEquals("phpunit", $object['menu']);
    $this->assertEquals("PHP Unit", $object['content']);

    // update
    $object["content"] = "Unit Testing";
    $this->action("create", $object);
    $updated = $this->db->query($this->model)->condition("id", $id)->one();
    $this->assertEquals("phpunit", $updated['menu']);
    $this->assertEquals("Unit Testing", $updated['content']);

    // add a few extras
    foreach (["First", "Second", "Third"] as $term) {
      $this->action("create", ["content" => $term." Menu", "menu" => "phpunit"]);
    }
    // we should now have 4
    $count = $this->query()->condition("menu", "phpunit")->count();
    $this->assertEquals(4, $count);

    // delete
    $this->action("delete", ["id" => $id]);
    // we should now have 3
    $count = $this->query()->condition("menu", "phpunit")->count();
    $this->assertEquals(3, $count);

    // delete taxonomy
    $this->action("delete_menu", ["menu" => "phpunit"]);
    // we should now have none
    $count = $this->query()->condition("menu", "phpunit")->count();
    $this->assertEquals(0, $count);
  }
}
