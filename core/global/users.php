<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/users.php
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
	return ((empty($group) && (!empty($_SESSION[P('id')]))) || (!empty($group) && $_SESSION[P('memberships')] & $groups[$group]));
}
/**
 * get user info
 * @ingroup users
 * @param string $field the name of the field
 */
function userinfo($field="") {
	global $groups;
	if ("group" == $field) return array_search($_SESSION[P('user')]['collective'], $groups);
	else return $_SESSION[P("user")][$field];
}
?>
