<?php
namespace Starbug\Core;
/**
* model factory interface
*/
interface ModelFactoryInterface {
	public function has($collection);
	public function get($collection);
}
