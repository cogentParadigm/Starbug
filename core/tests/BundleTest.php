<?php
namespace Starbug\Core\Tests;

use Starbug\Core\Bundle;

/**
 * Tests for Bundle.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class BundleTest extends \PHPUnit_Framework_TestCase {
  public function testEmpty() {
    $bundle = new Bundle();
    $this->assertTrue($bundle->isEmpty());
  }
  public function testNotEmpty() {
    $bundle = new Bundle(["key" => "value"]);
    $this->assertFalse($bundle->isEmpty());
  }
  public function testHas() {
    $bundle = new Bundle(['key' => 'value']);
    $this->assertTrue($bundle->has('key'));
    $this->assertFalse($bundle->has('nothere'));
  }
  public function testHasNested() {
    $bundle = new Bundle(['a' => ['b' => 'c']]);
    $this->assertTrue($bundle->has('a', 'b'));
    $this->assertFalse($bundle->has('a', 'c'));
    $this->assertFalse($bundle->has('a', 'b', 'c'));
  }
  public function testHasNestedFurther() {
    $bundle = new Bundle(['a' => ['b' => ['c' => 'd']]]);
    $this->assertTrue($bundle->has('a', 'b', 'c'));
    $this->assertFalse($bundle->has('a', 'b', 'd'));
    $this->assertFalse($bundle->has('a', 'b', 'c', 'd'));
  }
  public function testGet() {
    $bundle = new Bundle(['key' => 'value']);
    $this->assertEquals('value', $bundle->get('key'));
  }
  public function testGetNested() {
    $bundle = new Bundle(['a' => ['b' => 'c']]);
    $this->assertEquals('c', $bundle->get('a', 'b'));
  }
  public function testGetNestedFurther() {
    $bundle = new Bundle(['a' => ['b' => ['c' => 'd']]]);
    $this->assertEquals('d', $bundle->get('a', 'b', 'c'));
  }
  public function testSet() {
    $bundle = new Bundle();
    $bundle->set(['a' => ['b' => 'c']]);
    $this->assertEquals('c', $bundle->get('a', 'b'));
  }
  public function testSetNested() {
    $bundle = new Bundle();
    $bundle->set('a', ['b' => 'c']);
    $this->assertEquals('c', $bundle->get('a', 'b'));
  }
  public function testSetNestedFurther() {
    $bundle = new Bundle();
    $bundle->set('a', 'b', 'c');
    $this->assertEquals('c', $bundle->get('a', 'b'));
  }
}
