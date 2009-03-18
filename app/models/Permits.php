<?php
class Permits extends Table {

	var $defaults = array('who' => '0', 'status' => '0', 'priv_type' => 'table', 'related_id' => '0');
	var $uniques = array();
	var $lengths = array('role' => '30', 'action' => '100', 'priv_type' => '30', 'related_table' => '100');
	
	function create() {
		$permits = $_POST['permits'];
		/* more */
		return $this->store($permits);
	} 

	function delete() {
		return $this->remove("id='".$_POST['permits']['id']."'");
	}	
}
?>
