<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/init.php the standard init file. provides application wide functionality
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
// define SB_START_TIME to record application start time
defined('SB_START_TIME') or define('SB_START_TIME',microtime(true));

//define test mode as false
if (!defined('SB_TEST_MODE')) define('SB_TEST_MODE', false);

// define directory paths and set the include path
if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/core", "", dirname(__FILE__)));
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR);

// define STDOUT and STDIN if they are not defined
if (!defined('STDOUT')) define("STDOUT", fopen("php://stdout", "wb"));
if (!defined('STDIN')) define("STDIN", fopen("php://stdin", "r"));

// load configuration
include(BASE_DIR."/etc/Etc.php");

//define default database
if (!defined('DEFAULT_DATABASE')) define("DEFAULT_DATABASE", "default");

// set the default time zone
if (defined('Etc::TIME_ZONE')) date_default_timezone_set(Etc::TIME_ZONE);

//set the appropriate level of error reporting
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE | E_PARSE | E_ERROR);

include(BASE_DIR."/core/autoload.php");

//create locator
$locator = new ResourceLocator(BASE_DIR, $modules);

// autoload classes and global functions
foreach ($locator->locate("autoload.php", "") as $global_include) include($global_include);
foreach ($locator->locate("global_functions.php", "") as $global_include) include($global_include);

//create config

/**
 * instantiate the database to be passed to sb
 * @ingroup global
 */
$db = get_database(DEFAULT_DATABASE);

/**
 * global instance of the sb class
 * @ingroup global
 */
global $sb;
$sb = new sb($db);

$context = new Template($locator);
$context->assign("modules", $modules);
$context->assign("sb", $sb);

new ErrorHandler($context, defined('SB_CLI') ? "exception-cli" : "exception-html");

if (defined('SB_CLI')) {
	$sb->user = array("groups" => array("root"));
	$context->publish("init", "cli");
} else {
	$context->publish("init");
}

?>
