<?php
class Users extends UsersModel {

	function create() {
		$user = $_POST['users'];
		return $this->store($user);
	}

	function delete() {
		return $this->remove("id=".$_POST['users']['id']);
	}
}
?>
