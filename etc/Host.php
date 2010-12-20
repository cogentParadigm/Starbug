<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file etc/Host.php The host specific configuration file
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup etc
 */
/**
 * holds host specific configuration constants
 * access configuration constants via this class using Etc::CONSTANT_NAME
 * @ingroup etc
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

	/* script settings */
	const SIMPLETEST_ENABLED = false;
}
?>
