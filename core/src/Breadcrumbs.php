<?php
namespace Starbug\Core;

use IteratorAggregate;
use ArrayAccess;
use ArrayIterator;
use Traversable;

class Breadcrumbs implements IteratorAggregate, ArrayAccess {
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
  public function getIterator(): Traversable {
    return new ArrayIterator($this->crumbs);
  }
  public function offsetExists($offset): bool {
    return isset($this->crumbs[$offset]);
  }
  public function offsetGet($offset): mixed {
    return $this->crumbs[$offset];
  }
  public function offsetSet($offset, $value): void {
    $this->crumbs[$offset] = $value;
  }
  public function offsetUnset($offset): void {
    unset($this->crumbs[$offset]);
  }
}
