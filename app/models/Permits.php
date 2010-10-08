<?php
class Permits extends PermitsModel {

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
