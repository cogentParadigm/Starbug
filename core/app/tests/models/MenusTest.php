<?php
namespace Starbug\Core;

use Starbug\Core\Operation\Save;
use Starbug\Menus\Operation\DeleteMenus;

class MenusTest extends ModelTest {

  public $model = "menus";

  public function testCreateUpdateDelete() {
    // create
    $this->operation(Save::class, ["content" => "PHP Unit", "menu" => "phpunit"]);
    $id = $this->db->getInsertId("menus");
    $object = $this->db->query($this->model)->condition("id", $id)->one();
    // lets verify the explicit values were set
    $this->assertEquals("phpunit", $object['menu']);
    $this->assertEquals("PHP Unit", $object['content']);

    // update
    $object["content"] = "Unit Testing";
    $this->operation(Save::class, $object);
    $updated = $this->db->query($this->model)->condition("id", $id)->one();
    $this->assertEquals("phpunit", $updated['menu']);
    $this->assertEquals("Unit Testing", $updated['content']);

    // add a few extras
    foreach (["First", "Second", "Third"] as $term) {
      $this->operation(Save::class, ["content" => $term." Menu", "menu" => "phpunit"]);
    }
    // we should now have 4
    $count = $this->query()->condition("menu", "phpunit")->count();
    $this->assertEquals(4, $count);

    // delete
    $this->operation(DeleteMenus::class, ["id" => $id]);
    // we should now have 3
    $count = $this->query()->condition("menu", "phpunit")->count();
    $this->assertEquals(3, $count);

    // delete taxonomy
    $this->operation(DeleteMenus::class, ["menu" => "phpunit"]);
    // we should now have none
    $count = $this->query()->condition("menu", "phpunit")->count();
    $this->assertEquals(0, $count);
  }
}
