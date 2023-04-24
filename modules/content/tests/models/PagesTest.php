<?php
namespace Starbug\Content\Tests;

use Starbug\Core\ModelTest;
use Starbug\Core\Operation\Delete;
use Starbug\Core\Operation\Save;

class PagesTest extends ModelTest {
  public $model = "pages";
  public function testCreate() {
    $this->db->query("pages")->unsafeTruncate();
    $this->db->query("aliases")->unsafeTruncate();
    $this->operation(Save::class, ["path" => "phpunit", "blocks" => ["content-1" => "test"]]);
    $object = $this->db->query($this->model)->condition("pages.id", $this->db->getInsertId("pages"))->select("pages.path.alias as path")->one();
    // lets verify the explicit values were set
    $this->assertEquals("phpunit", $object['path']);
  }

  public function testUpdate() {
    $object = $this->db->query("pages")->condition("path.alias", "phpunit")->one();

    // test setting a normal field
    $object['title'] = "Test Title";
    // test setting block content
    $object['blocks']['content-1'] = "Test Content";
    $this->operation(Save::class, $object);
    // re fetch and test for updates
    $object = $this->db->query("pages")->condition("path.alias", "phpunit")->one();

    $block = $this->db->get("blocks", ["pages_id" => $object["id"]], ["limit" => "1"]);
    $this->assertEquals("Test Title", $object['title']);
    $this->assertEquals("Test Content", $block['content']);
  }

  public function testDelete() {
    // first assert that the record exists
    $object = $this->db->query("pages")->condition("path.alias", "phpunit")->one();
    $this->assertEquals(empty($object), false);

    // remove it and assert that the record is gone
    $this->operation(Delete::class, $object);
    $object = $this->db->query("pages")->condition("path.alias", "phpunit")->one();
    $this->assertEquals(empty($object), true);
  }
}
