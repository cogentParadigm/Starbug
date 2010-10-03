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
		$this->has_many("tags", "owner", "uris_tags", "tag_id");
		$this->has_many("uris", "owner", "uris_tags", "object_id");
	}

}
?>
