<?php
namespace Starbug\Core;

use Starbug\Core\Operation\DeleteTerms;
use Starbug\Core\Operation\Save;

class TermsTest extends ModelTest {

  public $model = "terms";

  public function testCreateUpdateDelete() {
    // create
    $this->operation(Save::class, ["term" => "PHP Unit", "taxonomy" => "phpunit"]);
    $id = $this->db->getInsertId("terms");
    $object = $this->db->query($this->model)->condition("id", $id)->one();
    // lets verify the explicit values were set
    $this->assertEquals("phpunit", $object['taxonomy']);
    $this->assertEquals("PHP Unit", $object['term']);

    // update
    $object["term"] = "Unit Testing";
    $this->operation(Save::class, $object);
    $updated = $this->db->query($this->model)->condition("id", $id)->one();
    $this->assertEquals("phpunit", $updated['taxonomy']);
    $this->assertEquals("Unit Testing", $updated['term']);

    // add a few extras
    foreach (["First", "Second", "Third"] as $term) {
      $this->operation(Save::class, ["term" => $term." Term", "taxonomy" => "phpunit"]);
    }
    // we should now have 4
    $count = $this->query()->condition("taxonomy", "phpunit")->count();
    $this->assertEquals(4, $count);

    // delete
    $this->operation(DeleteTerms::class, ["id" => $id]);
    // we should now have 3
    $count = $this->query()->condition("taxonomy", "phpunit")->count();
    $this->assertEquals(3, $count);

    // delete taxonomy
    $this->operation(DeleteTerms::class, ["taxonomy" => "phpunit"]);
    // we should now have none
    $count = $this->query()->condition("taxonomy", "phpunit")->count();
    $this->assertEquals(0, $count);
  }
}
