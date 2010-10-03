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
		"password" => "confirm:password_confirm  md5:  optional_update:",
		"owner" => "default:1",
		"status" => "default:4",
		"created" => "default:000-00-00 00:00:00",
		"modified" => "default:000-00-00 00:00:00"
	);

	function onload() {
		$this->has_many("users", "owner");
		$this->has_many("permits", "owner");
		$this->has_many("uris", "owner");
		$this->has_many("tags", "owner");
		$this->has_many("tags", "owner", "uris_tags", "tag_id");
		$this->has_many("uris", "owner", "uris_tags", "object_id");
		$this->has_many("leafs", "owner");
		$this->has_many("text_leaf", "owner");
		$this->has_many("files", "owner");
		$this->has_many("options", "owner");
	}

}
?>
