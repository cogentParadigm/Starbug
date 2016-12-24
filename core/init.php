<?php
// define base directory
if (!defined('BASE_DIR')) define('BASE_DIR', str_replace("/core", "", dirname(__FILE__)));

//set the appropriate level of error reporting
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE | E_PARSE | E_ERROR);

include(BASE_DIR."/core/autoload.php");

$factory = new Starbug\Core\ContainerFactory(str_replace("/core", "", dirname(__FILE__)));
$container = $factory->build($args);

date_default_timezone_set($container->get('time_zone'));
$container->get("Starbug\Core\ErrorHandler")->register();
