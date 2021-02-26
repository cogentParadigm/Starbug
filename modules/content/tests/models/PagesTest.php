<?php
namespace Starbug\Content;

use Starbug\Core\ModelTest;

class PagesTest extends ModelTest {
  public $model = "pages";
  public function testCreate() {
    $this->action("create", ["path" => "phpunit", "blocks" => ["content-1" => "test"]]);
    $object = $this->db->query($this->model)->condition("pages.id", $this->db->getInsertId("pages"))->select("pages.path.alias as path")->one();
    // lets verify the explicit values were set
    $this->assertEquals("phpunit", $object['path']);
  }

  public function testUpdate() {
    $object = $this->models->get("pages")->load(["path.alias" => "phpunit"]);

    // test setting a normal field
    $object['title'] = "Test Title";
    // test setting block content
    $object['blocks']['content-1'] = "Test Content";
    $this->action("create", $object);
    // re fetch and test for updates
    $object = $this->models->get("pages")->load(["path.alias" => "phpunit"], true);

    $block = $this->db->get("blocks", ["pages_id" => $object["id"]], ["limit" => "1"]);
    $this->assertEquals("Test Title", $object['title']);
    $this->assertEquals("Test Content", $block['content']);
  }

  public function testDelete() {
    // first assert that the record exists
    $object = $this->models->get("pages")->load(["path.alias" => "phpunit"]);
    $this->assertEquals(empty($object), false);

    // remove it and assert that the record is gone
    $this->action("delete", $object);
    $object = $this->models->get("pages")->load(["path.alias" => "phpunit"], true);
    $this->assertEquals(empty($object), true);
  }
}
