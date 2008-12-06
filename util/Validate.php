<?php
class Validate {
	function length($string, $minimum, $maximum) {
		$length = strlen($string);
		if($length >= $minimum && $length <= $maximum) return true;
		return false;
	}

	function email($email) {
		if(!eregi("^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\\.)+[a-z]{2,3}$", $email)) return false;
		else return true;
	}

	function username($username) {
		if(preg_match('/^[a-z0-9]+$/i', $username))
			return true;
		return false;
	}

	function toStore($string) {
		return str_replace('"', "&quot;", str_replace("\'", "&#39;", str_replace('\"', "&quot;", htmlspecialchars($string, ENT_NOQUOTES))));
	}
}
?>