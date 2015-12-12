<?php
namespace Starbug\Core;
class hook_store_required extends QueryHook {
	protected $models;
	function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	function empty_before_insert(&$query, $column, $argument) {
		if ($argument == "insert") $this->models->get($query->model)->error("This field is required.", $column);
	}
	function empty_before_update(&$query, $column, $argument) {
		if ($argument == "update") $this->models->get($query->model)->error("This field is required.", $column);
	}
	function empty_validate(&$query, $column, $argument) {
		if ($argument == "always") $this->models->get($query->model)->error("This field is required.", $column);
	}
	function store(&$query, $key, $value, $column, $argument) {
		if ($value === "" && empty($query->exclusions[$key])) $this->models->get($query->model)->error("This field is required", $column);
		return $value;
	}
}
?>
