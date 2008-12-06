<?php
class Elements extends Table {

	var $defaults = array('template' => '');

	function create() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');
		$element = $_POST['element'];
		return $this->store($element);
	}

	function delete() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');
		return $this->remove("id='".$_POST['element']['id']."'");
	}

}
?>