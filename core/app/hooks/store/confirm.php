<?php
class hook_store_confirm extends QueryHook {
	protected $models;
	function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	function validate(&$query, $key, $value, $column, $argument) {
		if ((isset($query->fields[$argument])) && ($value != $query->fields[$argument]))  $this->models->get($query->model)->error("Your $column"." fields do not match", $column);
		$query->exclude($argument);
		return $value;
	}
}
?>
