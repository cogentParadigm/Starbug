<?php
/**
* FILE: util/Validate.php
* PURPOSE: Data validation utility
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
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
		return mysql_real_escape_string($string);
	}
}
?>
