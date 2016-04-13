<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
namespace Starbug\Core;
class ViewsTest extends ModelTest {

	var $model = "views";

	function test_create() {
		$this->action("create", array("path" => "phpunit", "blocks" => array("content-1" => "test")));
		$object = $this->db->query($this->model)->condition("views.id", $this->insert_id)->select("views.path")->one();
		//lets verify the explicit values were set
		$this->assertEquals("phpunit", $object['path']);
	}

	function test_update() {
		$object = $this->models->get("views")->load(array("path" => "phpunit"));

		//test setting a normal field
		$object['title'] = "Test Title";
		//test setting block content
		$object['blocks']['content-1'] = "Test Content";
		$this->action("create", $object);
		//re fetch and test for updates
		$object = $this->models->get("views")->load(array("path" => "phpunit"), true);

		$block = $this->db->get("blocks", array("uris_id" => $object["uris_id"]), ["limit" => "1"]);
		$this->assertEquals("Test Title", $object['title']);
		$this->assertEquals("Test Content", $block['content']);
	}

	function test_delete() {
		//first assert that the record exists
		$object = $this->models->get("views")->load(array("path" => "phpunit"));
		$this->assertEquals(empty($object), false);

		//remove it and assert that the record is gone
		$this->action("delete", $object);
		$object = $this->models->get("views")->load(array("path" => "phpunit"), true);
		$this->assertEquals(empty($object), true);
	}

}
?>
