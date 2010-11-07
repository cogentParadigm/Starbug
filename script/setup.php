<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file script/setup.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */

	//CREATE FOLDERS & SET FILE PERMISSIONS
	$dirs = array("var", "var/xml", "var/models", "var/tmp", "var/public", "var/public/stylesheets", "app/public/uploads", "app/public/thumbnails");
	foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) exec("mkdir ".BASE_DIR."/".$dir);
	exec("chmod -R a+w ".BASE_DIR."/var ".BASE_DIR."/app/hooks ".BASE_DIR."/app/public/uploads ".BASE_DIR."/app/public/thumbnails");
	if (!file_exists(BASE_DIR."/var/migration")) {
		$file = fopen(BASE_DIR."/var/migration", "wb");
		fwrite($file, "0");
		fclose($file);
	}

	//INIT TABLES
	$schemer->migrate();

	$root = query("users", "where:username='root'  limit:1");
	if ($root['modified'] == $root['created']) { // PASSWORD HAS NEVER BEEN UPDATED
		//COLLECT USER INPUT
		fwrite(STDOUT, "\nPlease choose a root password:");
		$admin_pass = str_replace("\n", "", fgets(STDIN));
		fwrite(STDOUT, "\n\nYou may log in with these credentials -");
		fwrite(STDOUT, "\nusername: root");
		fwrite(STDOUT, "\npassword: $admin_pass\n\n");
		//UPDATE PASSWORD
		$errors = store("users", "password:$admin_pass", "username:root");
	}

?>
