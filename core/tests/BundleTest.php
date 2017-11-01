<?php
namespace Starbug\Core\Tests;
use Starbug\Core\Bundle;
class BundleTest extends \PHPUnit_Framework_TestCase {
	public function testHas() {
		$bundle = new Bundle(array('key' => 'value'));
		$this->assertTrue($bundle->has('key'));
		$this->assertFalse($bundle->has('nothere'));
	}
	public function testHasNested() {
		$bundle = new Bundle(array('a' => array('b' => 'c')));
		$this->assertTrue($bundle->has('a', 'b'));
		$this->assertFalse($bundle->has('a', 'c'));
		$this->assertFalse($bundle->has('a', 'b', 'c'));
	}
	public function testHasNestedFurther() {
		$bundle = new Bundle(array('a' => array('b' => array('c' => 'd'))));
		$this->assertTrue($bundle->has('a', 'b', 'c'));
		$this->assertFalse($bundle->has('a', 'b', 'd'));
		$this->assertFalse($bundle->has('a', 'b', 'c', 'd'));
	}
	public function testGet() {
		$bundle = new Bundle(array('key' => 'value'));
		$this->assertEquals('value', $bundle->get('key'));
	}
	public function testGetNested() {
		$bundle = new Bundle(array('a' => array('b' => 'c')));
		$this->assertEquals('c', $bundle->get('a', 'b'));
	}
	public function testGetNestedFurther() {
		$bundle = new Bundle(array('a' => array('b' => array('c' => 'd'))));
		$this->assertEquals('d', $bundle->get('a', 'b', 'c'));
	}
	public function testSet() {
		$bundle = new Bundle();
		$bundle->set(array('a' => array('b' => 'c')));
		$this->assertEquals('c', $bundle->get('a', 'b'));
	}
	public function testSetNested() {
		$bundle = new Bundle();
		$bundle->set('a', array('b' => 'c'));
		$this->assertEquals('c', $bundle->get('a', 'b'));
	}
	public function testSetNestedFurther() {
		$bundle = new Bundle();
		$bundle->set('a', 'b', 'c');
		$this->assertEquals('c', $bundle->get('a', 'b'));
	}
}
