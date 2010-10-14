<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/validate.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup validate
 */
/**
 * @defgroup validate
 * data validation utility
 * @ingroup util
 */
$sb->provide("util/validate");
/**
 * provides some data validation functions
 * @ingroup validate
 */
class validate {
	/**
	 * check the length of string against a min and max
	 * @param string $string the string to check
	 * @param int $minimum the minimum number of characters
	 * @param int $maximum the maximum number of characters
	 * @return bool true if the strings length is between minimum and maximum, false otherwise
	 */
	function length($string, $minimum, $maximum) {
		$length = strlen($string);
		if($length >= $minimum && $length <= $maximum) return true;
		return false;
	}
	/**
	 * check if a string fits the pattern of an email address
	 * @param string $email the string to check
	 * @return bool true if the string looks like an email address, false otherwise
	 */
	function email($email) {
		if(!eregi("^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\\.)+[a-z]{2,3}$", $email)) return false;
		else return true;
	}
}
?>
