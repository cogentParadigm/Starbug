<?php
namespace Starbug\Core;
//stores a URL path slug
class hook_store_path extends QueryHook {
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models, MacroInterface $macro, InputFilterInterface $filter) {
		$this->db = $db;
		$this->macro = $macro;
		$this->models = $models;
		$this->filter = $filter;
	}
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, $this->before_insert($query, $column, "", $column, $argument));
	}
	function before_insert(&$query, $key, $value, $column, $argument) {
		if (empty($value)) {
			$value = $this->generate($query, $column);
		}
		if (!is_numeric($value)) $query->exclude($key);
		return $value;
	}
	function before_update(&$query, $key, $value, $column, $argument) {
		$path = $this->macro->replace($argument, [$query->model => ["id" => $query->getId()]]);
		if (empty($value)) {
			$value = $this->generate($query, $column, $path);
		}
		if (!is_numeric($value)) {
			$value = $this->save($value, $path);
		}
		return $value;
	}
	function after_insert(&$query, $key, $value, $column, $argument) {
		$id = $query->getId();
		$path = $this->macro->replace($argument, [$query->model => ["id" => $id]]);
		if (!is_numeric($value)) {
			//value is not an ID, so we take it as a new path
			$value = $this->save($value, $path);
			$this->db->store($query->model, ["id" => $id, $key => $value]);
		}
		return $value;
	}

	function generate($query, $column, $path = false) {
		$pattern = $this->models->get($query->model)->hooks[$column]["pattern"];
		$data = [$query->model => $query->fields];
		$value = $this->macro->replace($pattern, $data);
		$value = strtolower(str_replace(" ", "-", $this->filter->normalize($value)));

		if (false !== $path) {
			$path = $this->macro->replace($path, $data);
		}

		$base = $value;
		$exists = $this->exists($value, $path);
		$count = 1;
		while ($exists->one()) {
			$value = $base."-".$count;
			$exists = $this->exists($value, $path);
			$count++;
		}
		return $value;
	}

	function save($value, $path) {
		if ($exists = $this->exists(["path" => $path])->one()) {
			$this->db->store("aliases", ["id" => $exists["id"], "alias" => $value]);
			return $exists["id"];
		} else {
			$this->db->store("aliases", ["alias" => $value, "path" => $path]);
			return $this->models->get("aliases")->insert_id;
		}
	}

	function exists($alias, $path = false) {
		$exists = $this->db->query("aliases");
		if (!is_array($alias)) {
			$alias = ["alias" => $alias];
		}
		$exists->conditions($alias);
		if (false !== $path) {
			$exists->condition("aliases.path", $path, "!=");
		}
		return $exists;
	}
}
?>
