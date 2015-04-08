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
$request = new Request($_SERVER['REQUEST_URI'], array(
	'server' => $_SERVER,
	'query' => $_GET,
	'data' => $_POST,
	'files' => $_FILES,
	'cookies' => $_COOKIES
));
$container->register("Request", $request, true);
$application = $container->get("ApplicationInterface");
$response = $application->handle($request);
$response->send();
?>
