<?php
// FILE: core/cli.php
/**
 *  init file for cli scripts
 * 
 *  @package StarbugPHP
 *  @subpackage core
 *  @author Ali Gangji <ali@neonrain.com>
 * 	@copyright 2008-2010 Ali Gangji
 */
	if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/core", "", dirname(__FILE__)));
	set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);
	if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
	if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));
	$autoload = array("util/db", "util/permits", "util/uris", "util/cli");
	include(BASE_DIR."/etc/Etc.php");
	include(BASE_DIR."/core/init.php");
	include(BASE_DIR."/core/db/Schemer.php");
	global $schemer;
	$schemer = new Schemer($sb->db);
?>
