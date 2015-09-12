<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file index.php index file. handles browser requests
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */

// include init file
include("core/init.php");

/**
 * global instance of the Request class
 * @ingroup global
 */
global $request;
$request = new Starbug\Core\Request($_SERVER['REQUEST_URI'], array(
	'server' => $_SERVER,
	'parameters' => $_GET,
	'data' => $_POST,
	'files' => $_FILES,
	'cookies' => $_COOKIE,
	'directory' => Etc::WEBSITE_URL
));
$container->set("Starbug\Core\Request", $request);
global $sb;
$sb = $container->get("Starbug\Core\sb");
$application = $container->get("Starbug\Core\ApplicationInterface");
$sb->start_session();
$response = $application->handle($request);
$response->send();
?>
