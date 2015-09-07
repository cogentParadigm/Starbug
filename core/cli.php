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

$result = include("init.php");

global $request;
$request = new Starbug\Core\Request("/", array(
	'server' => $_SERVER,
	'directory' => Etc::WEBSITE_URL
));
$container->register("Starbug\Core\Request", $request, true);
if ($result) {
	global $sb;
	$sb = $container->get("Starbug\Core\sb");
	$context->assign("sb", $sb);
	$sb->user = array("groups" => array("root"));
}
?>
