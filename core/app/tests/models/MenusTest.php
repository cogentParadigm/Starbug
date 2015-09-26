<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
namespace Starbug\Core;
class MenusTest extends ModelTest {

	var $model = "menus";

	function test_create_update_delete() {
		//create
		$this->action("create", array("content" => "PHP Unit", "menu" => "phpunit"));
		$id = $this->insert_id;
		$object = $this->db->query($this->model)->condition("id", $id)->one();
		//lets verify the explicit values were set
		$this->assertEquals("phpunit", $object['menu']);
		$this->assertEquals("PHP Unit", $object['content']);

		//update
		$object["content"] = "Unit Testing";
		$this->action("create", $object);
		$updated = $this->db->query($this->model)->condition("id", $id)->one();
		$this->assertEquals("phpunit", $updated['menu']);
		$this->assertEquals("Unit Testing", $updated['content']);

		//add a few extras
		foreach (array("First", "Second", "Third") as $term) {
			$this->action("create", array("content" => $term." Menu", "menu" => "phpunit"));
		}
		//we should now have 4
		$count = $this->query()->condition("menu", "phpunit")->count();
		$this->assertEquals(4, $count);

		//delete
		$this->action("delete", array("id" => $id));
		//we should now have 3
		$count = $this->query()->condition("menu", "phpunit")->count();
		$this->assertEquals(3, $count);

		//delete taxonomy
		$this->action("delete_menu", array("menu" => "phpunit"));
		//we should now have none
		$count = $this->query()->condition("menu", "phpunit")->count();
		$this->assertEquals(0, $count);
	}

}
?>
