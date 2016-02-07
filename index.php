<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file index.php index file. handles browser requests
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
use Starbug\Core\URL;
use Starbug\Core\Request;
// include init file
include("core/init.php");

$request = new Request(URL::createFromSuperGlobals(Etc::WEBSITE_URL));
$request->setHeaders($_SERVER)
				->setPost($_POST)
				->setFiles($_FILES)
				->setCookies($_COOKIE);

$container->set("Starbug\Core\RequestInterface", $request);
$application = $container->get("Starbug\Core\ApplicationInterface");
$response = $application->handle($request);
$response->send();
?>
