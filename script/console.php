<?php
	define('BASE_DIR', str_replace("/install", "", dirname(__FILE__)));
	set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);
	define("STDOUT", fopen("php://stdout", "wb"));
	define("STDIN", fopen("php://stdin", "r"));
	include("etc/Etc.php");
	include("core/init.php");
	include("core/db/Schemer.php");
	global $schemer;
	$schemer = new Schemer($sb->db);
	include("etc/schema.php");
	$sb->import("util/cli");
?>
