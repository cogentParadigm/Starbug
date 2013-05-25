<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file script/setup.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
	if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/script", "", dirname(__FILE__)));
	if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
	if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));
	$host = (file_exists(BASE_DIR."/etc/Host.php"));

	//CREATE FOLDERS & SET FILE PERMISSIONS
	$dirs = array("var", "var/xml", "var/json", "var/models", "var/tmp", "var/public", "var/public/stylesheets", "var/public/thumbnails", "app/hooks", "app/templates", "app/forms", "app/public/js", "app/public/uploads");
	foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) exec("mkdir ".BASE_DIR."/".$dir);
	exec("chmod -R a+w ".BASE_DIR."/var ".BASE_DIR."/app/public/uploads");

	if ($host) {
		//INIT TABLES
		$schemer->migrate();
	
		$root = query("users", "where:username='root'  limit:1");
		if (empty($root['password']) || $root['modified'] == $root['created']) { // PASSWORD HAS NEVER BEEN UPDATED
			//COLLECT USER INPUT
			fwrite(STDOUT, "\nPlease choose a root password:");
			$admin_pass = str_replace("\n", "", fgets(STDIN));
			fwrite(STDOUT, "\n\nYou may log in with these credentials -");
			fwrite(STDOUT, "\nusername: root");
			fwrite(STDOUT, "\npassword: $admin_pass\n\n");
			//UPDATE PASSWORD
			$errors = store("users", "password:$admin_pass", "username:root");
		}
	} else {
		fwrite(STDOUT, "\nHost file not found. Run 'sb generate host' to generate one.\n\n");
	}
?>
