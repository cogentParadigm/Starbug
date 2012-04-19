<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/init.php the standard init file. provides application wide functionality
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
if (defined("Etc::TIME_ZONE")) date_default_timezone_set(Etc::TIME_ZONE);
error_reporting(E_ALL ^ E_NOTICE);

// include core global functions
include(BASE_DIR."/core/global_functions.php");

// include module global functions
foreach (locate("global_functions.php", "") as $global_include) include($global_include);

// include the sb class
include(BASE_DIR."/core/sb.php");

/**
 * global instance of the sb class
 * @ingroup global
 */
global $sb;
$sb = new sb();

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

if (!is_array($autoload)) include(BASE_DIR."/etc/autoload.php");
if (!empty($autoload)) call_user_func_array(array($sb, "import"), $autoload);

$sb->publish("init");
?>
