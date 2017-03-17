<?php
namespace Starbug\Db\Query\Traits;

trait Parsing {
	/**
	 * Parse an expression containing a table name and associated clauses
	 * 'users as people on people.id=pages.owner' becomes:
	 * [
	 * 	"name" => "users",
	 * 	"alias" => "people",
	 * 	"on" => "people.id=pages.owner"
	 * ]
	 */
	protected function parseName($name) {
		$parts = explode(' ', $name);
		$on = $join = "";
		$alias = false;
		$count = count($parts);
		if ($count > 2 && strtolower($parts[$count-2]) == "on") {
			$on = array_pop($parts);
			array_pop($parts);
			$count -= 2;
			$alias = implode(' ', $parts);
		}
		if ($count > 2 && strtolower($parts[$count-2]) == "as") {
			$alias = array_pop($parts);
			array_pop($parts);
			$count -= 2;
		}
		$name = implode(' ', $parts);
		return array("name" => $name, "alias" => $alias, "on" => $on);
	}
}