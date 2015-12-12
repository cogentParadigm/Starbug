<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/cli.php init file for cli scripts
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
	if (!defined("SB_CLI")) define("SB_CLI", true);

include("init.php");

global $request;
$request = new Starbug\Core\Request("/", array(
	'server' => $_SERVER,
	'directory' => Etc::WEBSITE_URL
));
$container->set("Starbug\Core\Request", $request);
$user = $container->get("Starbug\Core\UserInterface");
$user->setUser(array("id" => "NULL", "groups" => array("root")));
?>
