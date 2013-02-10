<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/users/global_functions.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup users
 */
/**
 * @defgroup users
 * global functions
 * @ingroup global
 */
/**
 * check if user is logged in
 * @ingroup users
 * @param string $group return true only if the user is in the specified group (optional)
 */
function logged_in($group="") {
	global $groups;
	return ((empty($group) && (sb()->user)) || (!empty($group) && sb()->user['memberships'] & $groups[$group]));
}
/**
 * get user info
 * @ingroup users
 * @param string $field the name of the field
 */
function userinfo($field="") {
	global $groups;
	if (!sb()->user) return false;
	if ("group" == $field) return array_search(sb()->user['collective'], $groups);
	else return sb()->user[$field];
}
?>
