<?php
/**
 * permits model
 * 
 * @package Starbug
 * @subpackage models
 */
class PermitsModel extends Table {

	var $filters = array(
		"role" => "length:30",
		"who" => "default:0",
		"action" => "length:100",
		"priv_type" => "length:30  default:table",
		"related_table" => "length:100",
		"related_id" => "default:0",
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
