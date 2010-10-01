<?php
/**
 * users model
 * 
 * @package Starbug
 * @subpackage models
 */
class Users extends Table {

	var $filters = array(
		"username" => "length:128",
		"email" => "length:128  unique:",
		"password" => "confirm:password_confirm  md5:  optional_update:"
	);

	function onload() {
		$this->has_many("uris_tags", "owner");
	}

}
?>
