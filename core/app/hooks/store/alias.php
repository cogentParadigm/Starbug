<?php
namespace Starbug\Core;
class hook_store_alias extends QueryHook {
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
	}
	function validate(&$query, $key, $value, $column, $alias) {
		if (!empty($value) && !is_numeric($value) && $value != "NULL") {
			$referenced_model = explode(" ", $this->models->get($query->model)->hooks[$column]["references"]);
			// $alias might be '%first_name% %last_name%'
			$alias = explode("%", $alias);
			$match = '';
			$num = 1;
			while (!empty($alias)) {
				$next = array_pop($alias);
				if ($num % 2 == 0) { //match column
					if (empty($match)) $match = "$next";
					else $match = "concat($next, $match)";
				} else if (!empty($next)) { // in between string
					if (empty($match)) $match = "'$next'";
					else $match = "concat('$next', $match)";
				}
				$num++;
			}
			$row = $this->db->query($referenced_model[0])->select($referenced_model[1])->condition($match, $value)->one();
			if (!empty($row)) $value = $row[$referenced_model[1]];
		}
		return $value;
	}
}
?>
