<?php
class Uris extends Table {

	var $defaults = array('visible' => '1', 'importance' => '0');
	var $uniques = array();
	var $lengths = array('path' => '64', 'template' => '32', 'visible' => '2', 'importance' => '2');

	function create() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');
		$uris = $_POST['uris'];
		return $this->store($uris);
	}

	function delete() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');
		return $this->remove("id='".$_POST['uris']['id']."'");
	}

}
?>