<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/cli.php init file for cli scripts
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
	if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/core", "", dirname(__FILE__)));
	set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);
	if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
	if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));
	// include configuration file
	include(BASE_DIR."/etc/Etc.php");
	// include init file
	include(BASE_DIR."/core/init.php");
	//restore error handlers
	restore_error_handler();
	restore_exception_handler();
	//import cli utils
	$sb->import("util/cli");
	//publish cli init hook
	$sb->publish("cli.init");
?>
