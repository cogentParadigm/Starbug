<?php
	include("etc/Etc.php");
	include("core/init.php");
	include("core/db/Schemer.php");
	global $schemer;
	$schemer = new Schemer($sb->db);
	include("etc/schema.php");
	$sb->import("util/cli");
?>
