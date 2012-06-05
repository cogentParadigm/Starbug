<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/cache.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup cache
 */
/**
 * @defgroup cache
 * global functions
 * @ingroup global
 */
/**
 * get or save a cached value
 * @ingroup cache
 * @param string $key the key you want to get or save. use periods to group key names.
 * @param string $value (optional) value to store
 * @param string $expiry (optional) expiry time from now (default is 30 days)
 * @param string $dir the cache directory
 * @return string the value
 */
function cache($key, $value=null, $expiry=null, $dir="var/cache/") {
	if ($value == null) { //GET THE VALUE
		if (file_exists(BASE_DIR."/".$dir.$key.".time") && file_exists(BASE_DIR."/".$dir.$key.".cache")) {
			$expired = (int) file_get_contents(BASE_DIR."/".$dir.$key.".time");
			$now = time();
			if ($expired > $now) $value = json_decode(file_get_contents(BASE_DIR."/".$dir.$key.".cache"), true);
		}
	} else { //SET THE VALUE
		//SET EXPIRY
		if ($expiry == null) $expiry = 30*24*60*60; //DEFAULT 30 DAYS
		$expiry = time() + $expiry;
		//CREATE DIRS
		$dirs = explode("/", $key);
		$start = BASE_DIR."/".$dir;
		foreach ($dirs as $d) {
			if (!file_exists($start.$d)) mkdir($start.$d);
			$start .= $d."/";
		}
		//STORE VALUE & EXPIRY
		file_put_contents(BASE_DIR."/".$dir.$key.".time", $expiry);
		file_put_contents(BASE_DIR."/".$dir.$key.".cache", json_encode($value));
	}
	return $value;
}
/**
 * check if a key has an active cache
 * @ingroup cache
 * @param string $key the key you want to get or save. use periods to group key names.
 * @param string $dir the cache directory
 * @return bool true if the value is cached, false otherwise
 */
function is_cached($key, $dir="var/cache/") {
	$key = str_replace(".", "/", $key);
	if (file_exists(BASE_DIR."/".$dir.$key.".time") && file_exists(BASE_DIR."/".$dir.$key.".cache")) {
		$expired = (int) file_get_contents(BASE_DIR."/".$dir.$key.".time");
		$now = time();
		if ($expired > $now) return true;
	}
	return false;
}
?>
