<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file util/starr.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup starr
 */
/**
 * @defgroup starr
 * string to array conversion utility
 * @ingroup util
 */
/**
 * provides string to array conversion
 * @ingroup starr
 */
class starr {
	/**
	 * provides string to array conversion
	 * @param string $str string of key/value pairs where the key is separate from the value by a colon (:) and the pairs are separated by 2 spaces
	 */
	function star($str="") {
		if (is_array($str)) return $str;
		$arr = array();
		$keypairs = explode("  ", $str);
		foreach($keypairs as $keypair) {
			if (false !== ($pos = strpos($keypair, ":"))) {
				$key = substr($keypair, 0, $pos);
				$value = substr($keypair, $pos+1);
				if ($value == "false") $value = false;
				if ($value == "true") $value = true;
				$arr[$key] = $value;
			} else if ($keypair!="") $arr[] = $keypair;
		}
		return $arr;
	}
	/**
	 * used by rstar to find the closing \t for a nested array
	 * @param string $str the string to search
	 * @param int $offset character offset
	 * @private
	 */
	private function closer_pos($str, $offset=0) {
		$close = strpos($str, "\t");
		$nextopen = strpos($str, "\n");
		if (($nextopen === false) || ($nextopen > $close)) return $offset+$close; else return $offset+starr::closer_pos(substr($str, $close+1), $close+1);
	}
	/**
	 * provides recursive string to array conversion that allows nested arrays
	 * @param string $str key/value pairs like starr::star but you can open arrays with \n and close them with \t
	 */
	function rstar($str="") {
		$arr = array(); $open = strpos($str, "\n"); $next = $open-1;
		if ($open===false) return starr::star($str);//no nesting, return flat array
		else $close = starr::closer_pos(substr($str, $open+1), $open+1);//find the close of the first open
		if ($open != 0) {//something is before the first open
			while (($next > 0) && (($str{$next} != " ") || ($str{$next+1} != " "))) $next--;
			if ($next != 0) $arr = starr::star(substr($str, 0, $next));
		}
		if ($next != 0) $next++;
		$arr = (($str{$open-1} == ":") ? array_merge($arr, array(substr($str, $next, ($open-1)-$next) => starr::rstar(substr($str, $open+1, $close-($open+1))))) : array_merge($arr, Starr::rstar(substr($str, $open+1, $close-($open+1)))));
		if ((strlen($str) > ($close+1)) && ($str{$close+1} == " ")) $arr = array_merge($arr, starr::rstar(substr($str, $close+3)));
		return $arr;
	}

}
?>
