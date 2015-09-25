<?php
namespace Starbug\Core;
//stores a URL path slug
class hook_store_slug extends QueryHook {
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models, MacroInterface $macro) {
		$this->db = $db;
		$this->macro = $macro;
		$this->models = $models;
	}
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, $this->validate($query, $column, "", $column, $argument));
	}
	function validate(&$query, $key, $value, $column, $argument) {
		if (empty($value) && isset($query->fields[$argument])) {
			$value = $query->fields[$argument];

			$value = strtolower(str_replace(" ", "-", normalize($value)));

			if (!empty($this->models->get($query->model)->hooks[$column]["pattern"])) {
				$pattern = $this->models->get($query->model)->hooks[$column]["pattern"];
				$data = array($query->model => array_merge($query->fields, array($column => $value)));
				$value = $this->macro->replace($pattern, $data);
			}

			$base = $value;
			$exists = $this->exists($query, $column, $value);
			$count = 2;
			while ($exists->one()) {
				$value = $base."-".$count;
				$exists = $this->exists($query, $column, $value);
				$count++;
			}
		}
		return $value;
	}

	function exists($query, $column, $value) {
			$exists = $this->db->query($query->model)->condition($query->model.".".$column, $value);
			$id = 0;
			if ($query->mode == "update") {
				$id = $query->getId();
				$exists->condition($query->model.".id", $id, "!=");
			}
			if (!empty($this->models->get($query->model)->hooks[$column]["unique"])) {
				$parts = explode(" ", $this->models->get($query->model)->hooks[$column]["unique"]);
				foreach ($parts as $c) if (!empty($c)) $exists->condition($c, $query->fields[$c]);
			}
			return $exists;
	}
}
?>
