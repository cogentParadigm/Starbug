<?php
namespace Starbug\Core;
class Breadcrumbs implements \IteratorAggregate, \ArrayAccess {
	protected $crumbs = [];
	public function add($crumb) {
		$this->crumbs[] = $crumb;
	}
	public function insert($index, $crumb) {
		array_splice($this->crumbs, $index, 0, [$crumb]);
	}
	public function remove($index) {
		array_splice($this->crumbs, $index, 1);
	}
	public function get($index) {
		return $this->crumbs[$index];
	}
	public function getIterator() {
		return new \ArrayIterator($this->crumbs);
	}
	public function offsetExists($offset) {
		return isset($this->crumbs[$offset]);
	}
	public function offsetGet($offset) {
		return $this->crumbs[$offset];
	}
	public function offsetSet($offset, $value) {
		$this->crumbs[$offset] = $value;
	}
	public function offsetUnset($offset) {
		unset($this->crumbs[$offset]);
	}
}
