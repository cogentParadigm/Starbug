<?php
/**
* FILE: core/cli.php
* PURPOSE: init file for cli scripts
* 
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
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
	define('BASE_DIR', str_replace("core", "", dirname(__FILE__)));
	set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);
	if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
	if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));
	$autoload = array("util/permits", "util/uris", "util/cli");
	include(BASE_DIR."/etc/Etc.php");
	include(BASE_DIR."/core/init.php");
	include(BASE_DIR."/core/db/Schemer.php");
	global $schemer;
	$schemer = new Schemer($sb->db);
	$migrations = unserialize(reset($sb->query("options", "select:value  where:name='migrations'  limit:1")));
	call_user_func_array(array($schemer, "add_migrations"), $migrations);
?>
