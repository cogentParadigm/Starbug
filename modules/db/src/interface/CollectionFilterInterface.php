<?php
namespace Starbug\Core;
/**
* model factory interface
*/
interface CollectionFilterInterface {
	public function filterQuery($collection, $query, &$ops);
	public function filterRows($collection, $rows);
}
