<?php
/**
 * standard delete function for models
 * 
 * @package StarbugPHP
 * @subpackage plugins
 */
class StandardDelete {

	function delete($model) {
		return $model->remove("id='".$_POST[$model->type]['id']."'");
	}

}
?>
