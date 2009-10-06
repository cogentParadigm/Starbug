<?php
class Uris extends Table {

	var $filters = array(
		"path" => "length:64",
		"template" => "length:32",
		"importance" => "length:2",
		"check_path" => "default:1",
		"prefix" => "default:/app/nouns/",
		"parent" => "deflaut:0"
	);

	function Uris($type) {
		parent::Table($type);
		$this->has_many("system_tags", "object_id", "uris_tags", "tag_id");
	}

	function create() {
		$uris = $_POST['uris'];
		$uris['owner'] = $_SESSION[P("id")];
		return $this->store($uris);
	}

	function delete() {
		return $this->remove("id='".$_POST['uris']['id']."'");
	}

}
?>
