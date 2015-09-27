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

$di = include(BASE_DIR."/etc/di.php");
$locator = new Starbug\Core\ResourceLocator($di['base_directory'], $di['modules']);
$builder = new DI\ContainerBuilder();
$builder->addDefinitions($di);
foreach ($locator->locate("di.php", "etc") as $defs) $builder->addDefinitions($defs);
$container = $builder->build();

$container->set('Interop\Container\ContainerInterface', $container);
$container->set('Starbug\Core\ResourceLocatorInterface', $locator);
$container->get("Starbug\Core\ErrorHandler")->register();

?>
