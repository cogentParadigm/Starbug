<?php
	if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/script", "", dirname(__FILE__)));
	set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);
	if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
	if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));
	include("etc/Etc.php");
	include("core/init.php");
	include("core/db/Schemer.php");
	global $schemer;
	$schemer = new Schemer($sb->db);
	$to = reset($sb->query("options", "select:value  where:name='migration'  limit:1"));
	//MOVE TO CURRENT MIGRATION
	$current = 0;
	while ($current < $to) {
		$migration = new $schemer->migrations[$current]();
		$migration->up();
		$current++;
	}
	$sb->import("util/cli");
?>
