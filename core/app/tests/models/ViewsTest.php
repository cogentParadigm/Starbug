<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
import("lib/test/ModelTest", "core");
class ViewsTest extends ModelTest {

	var $model = "views";

	function test_create() {
		$this->action("create", array("path" => "phpunit"));
		$object = query($this->model)->condition("id", $this->insert_id)->one();
		//lets verify the explicit values were set
		$this->assertEquals("phpunit", $object['path']);
	}

	function test_update() {
		$object = $this->get("path:phpunit", "limit:1");

		//test setting a normal field
		$object['title'] = "Test Title";
		//test setting block content
		$object['blocks']['content-1'] = "Test Content";
		$this->action("create", $object);
		//re fetch and test for updates
		$object = $this->get("path:phpunit", "limit:1");

		$block = get("blocks", array("uris_id" => $object["uris_id"]), "limit:1");
		$this->assertEquals("Test Title", $object['title']);
		$this->assertEquals("Test Content", $block['content']);
	}

	function test_delete() {
		//first assert that the record exists
		$object = $this->get("path:phpunit", "limit:1");
		$this->assertEquals(empty($object), false);

		//remove it and assert that the record is gone
		$this->action("delete", $object);
		$object = $this->get("path:phpunit", "limit:1");
		$this->assertEquals(empty($object), true);
	}

}
?>
