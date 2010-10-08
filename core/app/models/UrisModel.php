<?php
/**
 * uris model
 * 
 * @package Starbug
 * @subpackage models
 */
class UrisModel extends Table {

	var $filters = array(
		"path" => "length:64",
		"template" => "length:64",
		"title" => "length:128",
		"parent" => "default:0",
		"sort_order" => "default:0",
		"check_path" => "default:1",
		"prefix" => "length:128  default:app/views/",
		"owner" => "default:1  references:users id",
		"status" => "default:4",
		"created" => "default:000-00-00 00:00:00",
		"modified" => "default:000-00-00 00:00:00"
	);

	function onload() {
		$this->has_one("users", "owner");
		$this->has_many("tags", "object_id", "uris_tags", "tag_id");
		$this->has_many("users", "object_id", "uris_tags", "owner");
	}

}
?>
