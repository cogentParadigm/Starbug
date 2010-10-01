<?php
/**
 * standard create/update function for models
 * 
 * @package StarbugPHP
 * @subpackage plugins
 */
class StandardCreate {

	function create($model) {
		$record = $_POST[$model->type];
		return $model->store($record);
	}

}
?>
