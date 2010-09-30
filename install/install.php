#!/usr/bin/php
<?php
// FILE: install/intall.php
/**
 * install script
 * 
 * @package StarbugPHP
 * @subpackage install
 * @author Ali Gangji <ali@neonrain.com>
 * @copyright 2008-2010 Ali Gangji
 */
	if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/install", "", dirname(__FILE__)));
	set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);
	if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
	if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));
	
	//CREATE FOLDERS & SET FILE PERMISSIONS
	exec("chmod a+x script/generate");
	exec("chmod ug+s script/cgenerate");
	$dirs = array("var", "var/hooks", "var/xml", "app/public/uploads", "app/public/thumbnails");
	foreach ($dirs as $dir) if (!file_exists(BASE_DIR."/".$dir)) exec("mkdir ".BASE_DIR."/".$dir);
	exec("chmod -R a+w ".BASE_DIR."/var ".BASE_DIR."/app/public/uploads ".BASE_DIR."/app/public/thumbnails");

	//INIT TABLES
	include(BASE_DIR."/etc/Etc.php");
	include(BASE_DIR."/core/init.php");
	include(BASE_DIR."/core/db/Schemer.php");
	global $schemer;
	$schemer = new Schemer($sb->db);
	$schemer->add_migrations("CoreMigration");
	$migration = new CoreMigration();
	$migration->up();
	$schemer->update();
	$migration->created();
	
	//SUSBSCRIBE HOOKS
	$sb->import("util/subscribe");
	$sb->subscribe("header", "global", 10, "sb::load", "core/app/hooks/header");
	$sb->subscribe("footer", "global", 10, "sb::load", "core/app/hooks/footer");
	$sb->subscribe("footer", "dojo", 10, "sb::load", "core/app/hooks/dojo.footer");
?>
