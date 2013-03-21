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
 * @ingroup core
 */
/**
 * get or save a cached value
 * @ingroup cache
 * @param string $key the key you want to get or save. use periods to group key names.
 * @param string $value (optional) value to store
 * @param string $ttl (optional) store for $ttl seconds, otherwise it will persist until removed
 * 															 You can also specify a string like "30 days"
 * @return mixed if $value is not specified, the value will be return
 *               if $value is specified and stored successfully, true will be returned
 *               a failure in either case will return false
 */
function cache($key, $value=null, $ttl=0) {
	return $value;
	if ($value == null) {
		return apc_fetch(BASE_DIR.'_'.$key); //GET THE VALUE
	} else {
		if (!is_numeric($ttl)) $ttl = strtotime($ttl) - time(); //SET EXPIRY
		return apc_store(BASE_DIR.'_'.$key, $value, $ttl); //STORE VALUE
	}
	return $value;
}
/**
 * check if a key has an active cache
 * @ingroup cache
 * @param string $key the key you want to get or save. use periods to group key names.
 * @return bool true if the value is cached, false otherwise
 */
function is_cached($key) {
	return false;
	return apc_exists(BASE_DIR.'_'.$key);
}
/**
 * delete a key from the cache
 * @ingroup cache
 * @param string $key the key you want to delete
 * @return bool true if successful, false otherwise
 */
function cache_delete($key) {
	return apc_delete(BASE_DIR.'_'.$key);
}
?>
