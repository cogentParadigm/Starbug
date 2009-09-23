<?php
class Uris extends Table {

	var $defaults = array('visible' => '1', 'importance' => '0');
	var $uniques = array();
	var $lengths = array('path' => '64', 'template' => '32', 'visible' => '2', 'importance' => '2');

	function Uris($data, $t) {
		parent::Table($data, $t);
		$this->has_many("system_tags", "object_id", "uris_tags", "tag_id");
	}

	function create() {
		$uris = $_POST['uris'];
		$uris['owner'] = $_SESSION[P("id")];
		$errors = $this->store($uris);
		return $errors;
	}

	function delete() {
		return $this->remove("id='".$_POST['uris']['id']."'");
	}

}
?>
