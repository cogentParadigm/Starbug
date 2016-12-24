<?php
namespace Starbug\Content;
use Starbug\Core\ModelTest;
class PagesTest extends ModelTest {
	public $model = "pages";
	function test_create() {
		$this->action("create", array("path" => "phpunit", "blocks" => array("content-1" => "test")));
		$object = $this->db->query($this->model)->condition("pages.id", $this->insert_id)->select("pages.path.alias as path")->one();
		//lets verify the explicit values were set
		$this->assertEquals("phpunit", $object['path']);
	}

	function test_update() {
		$object = $this->models->get("pages")->load(array("path.alias" => "phpunit"));

		//test setting a normal field
		$object['title'] = "Test Title";
		//test setting block content
		$object['blocks']['content-1'] = "Test Content";
		$this->action("create", $object);
		//re fetch and test for updates
		$object = $this->models->get("pages")->load(array("path.alias" => "phpunit"), true);

		$block = $this->db->get("blocks", array("pages_id" => $object["id"]), ["limit" => "1"]);
		$this->assertEquals("Test Title", $object['title']);
		$this->assertEquals("Test Content", $block['content']);
	}

	function test_delete() {
		//first assert that the record exists
		$object = $this->models->get("pages")->load(array("path.alias" => "phpunit"));
		$this->assertEquals(empty($object), false);

		//remove it and assert that the record is gone
		$this->action("delete", $object);
		$object = $this->models->get("pages")->load(array("path.alias" => "phpunit"), true);
		$this->assertEquals(empty($object), true);
	}
}
