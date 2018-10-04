<?php
namespace Starbug\Core;
/**
* query builder factory interface
*/
interface QueryBuilderFactoryInterface {
	public function build($collection);
}
