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

$url = $container->make("Starbug\Core\URLInterface", array(
	'host' => $_SERVER['HTTP_HPOST'],
	'base_directory' => ETC::WEBSITE_URL
));
$url->setPath(substr($_SERVER['REQUEST_URI'], strlen(Etc::WEBSITE_URL)));
$url->setParameters($_GET);

$request = $container->make("Starbug\Core\RequestInterface", array('url' => $url));
$request->setHeaders($_SERVER)
				->setPost($_POST)
				->setFiles($_FILES)
				->setCookies($_COOKIE);

$container->set("Starbug\Core\Request", $request);
$application = $container->get("Starbug\Core\ApplicationInterface");
$response = $application->handle($request);
$response->send();
?>
