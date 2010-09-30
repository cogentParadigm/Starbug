<?php
// FILE: etc/Host.php
/**
 * This is the host specific configuration file
 * 
 * @package StarbugPHP
 * @subpackage etc
 * @author Ali Gangji <ali@neonrain.com>
 * @copyright 2008-2010 Ali Gangji
 */
class Host {
	/* details for database */
	const DB_TYPE = "mysql";
	const DB_HOST = "localhost";
	const DB_USERNAME = "root";
	const DB_PASSWORD = "";
	const DB_NAME = "test";
	const PREFIX = "sb_";

	/* URL of website */
	const WEBSITE_URL = "localhost/";

	/* Java paths */
	const JAVA_PATH = "/usr/bin/java";
	const SAXON_PATH = "/usr/local/saxon/saxon9.jar";
}
?>
