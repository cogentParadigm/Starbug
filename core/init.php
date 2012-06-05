<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/init.php the standard init file. provides application wide functionality
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
defined('SB_START_TIME') or define('SB_START_TIME',microtime(true));
if (defined('Etc::TIME_ZONE')) date_default_timezone_set(Etc::TIME_ZONE);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);

// include the sb class
include(BASE_DIR."/core/sb.php");

// load global functions
include(BASE_DIR."/core/global_functions.php");

/**
 * list of groups from etc/groups.json
 * @ingroup global
 */
global $groups;
$groups = config("groups");

/**
 * list of statuses from etc/statuses.json
 * @ingroup global
 */
global $statuses;
$statuses = config("statuses");

/**
 * global instance of the sb class
 * @ingroup global
 */
global $sb;
$sb = new sb();

//publish init hooks
$sb->publish("init");

?>
