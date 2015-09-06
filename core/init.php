<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/init.php the standard init file. provides application wide functionality
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */

// define base directory
if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/core", "", dirname(__FILE__)));

// load host configuration
include(BASE_DIR."/etc/Etc.php");

//define default database
if (!defined('DEFAULT_DATABASE')) define("DEFAULT_DATABASE", "default");

// set the default time zone
if (defined('Etc::TIME_ZONE')) date_default_timezone_set(Etc::TIME_ZONE);

//set the appropriate level of error reporting
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE | E_PARSE | E_ERROR);

include(BASE_DIR."/core/autoload.php");

$container = new Container();
$container->register('base_directory', BASE_DIR, true);
$container->register('modules', $modules, true);
$container->register('database_name', DEFAULT_DATABASE, true);

//create locator
$locator = $container->get('ResourceLocatorInterface');

// global functions
foreach ($locator->locate("global_functions.php", "") as $global_include) include($global_include);

if (file_exists(BASE_DIR."/var/autoload_classmap.php")) {
	$loader = $container->get("AutoloaderInterface");
	$loader->add(include(BASE_DIR."/var/autoload_classmap.php"));
	$loader->register();

	$context = $container->get("TemplateInterface");
	$context->assign("container", $container);

	new ErrorHandler($context, defined('SB_CLI') ? "exception-cli" : "exception-html");
} else {
	return false;
}
return true;

?>
