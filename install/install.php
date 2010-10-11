#!/usr/bin/php
<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file install/install.php cli installer script
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
	if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/install", "", dirname(__FILE__)));
	set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);
	if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
	if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));
	
	//CREATE FOLDERS & SET FILE PERMISSIONS
	exec("chmod a+x script/generate");
	exec("chmod ug+s script/cgenerate");
	$dirs = array("var", "var/xml", "app/public/uploads", "app/public/thumbnails");
	foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) exec("mkdir ".BASE_DIR."/".$dir);
	exec("chmod -R a+w ".BASE_DIR."/var ".BASE_DIR."/app/hooks ".BASE_DIR."/app/public/uploads ".BASE_DIR."/app/public/thumbnails");
	if (!file_exists(BASE_DIR."/var/migration")) exec("echo 0 > ".BASE_DIR."/var/migration");

	//LOAD CORE
	include(BASE_DIR."/etc/Etc.php");
	include(BASE_DIR."/core/init.php");
	include(BASE_DIR."/core/db/Schemer.php");

	//INIT TABLES
	global $schemer;
	$schemer = new Schemer($sb->db);
	$schemer->migrate();

	//COLLECT USER INPUT
	fwrite(STDOUT, "\nPlease choose a root password:");
	$admin_pass = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "\n\nYou may log in with these credentials -");
	fwrite(STDOUT, "\nusername: root");
	fwrite(STDOUT, "\npassword: $admin_pass\n\n");
	//UPDATE PASSWORD
	$errors = store("users", "password:$admin_pass", "username:root");

?>
