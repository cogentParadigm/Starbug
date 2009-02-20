<?php
class Uris extends Table {

	var $defaults = array('visible' => '1', 'importance' => '0');
	var $uniques = array();
	var $lengths = array('path' => '64', 'template' => '32', 'visible' => '2', 'importance' => '2');

	function create() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');
		$uris = $_POST['uris'];
		$errors = $this->store($uris);
		if (empty($errors)) {
			$path = dirname(__FILE__)."/../nouns/".$uris['path'].".php";
			$template = dirname(__FILE__)."/../nouns/".$uris['template'].".php";
			if (!file_exists($path)) {
				$file = fopen($path, "w");
				fclose($file);
			}
			if (!file_exists($template)) {
				$file = fopen($template, "w");
				fclose($file);
			}
		}
		return $errors;
	}

	function delete() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');
		return $this->remove("id='".$_POST['uris']['id']."'");
	}

}
?>