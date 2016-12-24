<?php
namespace Starbug\Core;
class TermsTest extends ModelTest {

	public $model = "terms";

	function test_create_update_delete() {
		//create
		$this->action("create", array("term" => "PHP Unit", "taxonomy" => "phpunit"));
		$id = $this->insert_id;
		$object = $this->db->query($this->model)->condition("id", $id)->one();
		//lets verify the explicit values were set
		$this->assertEquals("phpunit", $object['taxonomy']);
		$this->assertEquals("PHP Unit", $object['term']);

		//update
		$object["term"] = "Unit Testing";
		$this->action("create", $object);
		$updated = $this->db->query($this->model)->condition("id", $id)->one();
		$this->assertEquals("phpunit", $updated['taxonomy']);
		$this->assertEquals("Unit Testing", $updated['term']);

		//add a few extras
		foreach (array("First", "Second", "Third") as $term) {
			$this->action("create", array("term" => $term." Term", "taxonomy" => "phpunit"));
		}
		//we should now have 4
		$count = $this->query()->condition("taxonomy", "phpunit")->count();
		$this->assertEquals(4, $count);

		//delete
		$this->action("delete", array("id" => $id));
		//we should now have 3
		$count = $this->query()->condition("taxonomy", "phpunit")->count();
		$this->assertEquals(3, $count);

		//delete taxonomy
		$this->action("delete_taxonomy", array("taxonomy" => "phpunit"));
		//we should now have none
		$count = $this->query()->condition("taxonomy", "phpunit")->count();
		$this->assertEquals(0, $count);
	}
}
