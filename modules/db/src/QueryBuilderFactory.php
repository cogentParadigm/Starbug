<?php
namespace Starbug\Core;
/**
* an implementation of QueryBuilderFactoryInterface
*/
class QueryBuilderFactory implements QueryBuilderFactoryInterface {
	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	public function build($collection) {
		return new QueryBuilder($this->db, $collection);
	}
}
