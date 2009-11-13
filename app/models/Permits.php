<?php
class Permits extends Table {
	var $filters = array(
		"priv_type" 		=> "default:table",
		"who" 					=> "default:0",
		"status" 				=> "default:0",
		"related_id" 		=> "default:0"
	);
	function grant() {
		$permit = $_POST['permits'];
		if ($permit['priv_type'] != "object") $permit['related_id'] = '0';
		return $this->store($permit);
	} 
	function delete() {
		$this->remove("id='".$_POST['permits']['id']."'");
		return array();
	}
}
?>
