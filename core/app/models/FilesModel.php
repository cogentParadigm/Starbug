<?php
/**
 * files model
 * 
 * @package Starbug
 * @subpackage models
 */
class FilesModel extends Table {

	var $filters = array(
		"mime_type" => "length:128",
		"filename" => "length:128",
		"caption" => "length:255",
		"owner" => "default:1  references:users id",
		"status" => "default:4",
		"created" => "default:000-00-00 00:00:00",
		"modified" => "default:000-00-00 00:00:00"
	);

	function onload() {
		$this->has_one("users", "owner");
	}

}
?>
