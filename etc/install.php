#!/usr/bin/php
<?php
/**
* FILE: etc/install.php
* PURPOSE: This is the installation file
* NOTE: you should run this from the command line, and then delete it.
* 			If you need to reinstall, you can get a copy later.
*
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/

	//COLLECT USER INPUT
	fwrite(STDOUT, "\nWelcom to the StarbugPHP Installer\nPlease enter the following information:\n\n");
	fwrite(STDOUT, "Super Admin Email:");
	$admin_email = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "Super Admin Password:");
	$admin_pass = md5(str_replace("\n", "", fgets(STDIN)));
	
	//CREATE FOLDERS & SET FILE PERMISSIONS
	exec("chmod a+x script/generate");
	exec("chmod ug+s script/cgenerate");
	exec("mkdir var var/hooks app/public/uploads app/public/thumbnails");
	exec("chmod -R a+w var app/public/uploads app/public/thumbnails");

	//INIT TABLES
	include("etc/Etc.php");
	include("etc/init.php");
	include("core/db/Schemer.php");
	$schemer = new Schemer($sb->db);
	include("etc/schema.php");
	$schemer->update();

	//INSERT RECORDS
	//ADMIN USER
	$sb->store("users", array(
		"email" => $admin_email,
		"password" => $admin_pass,
		"memberships" => 1
	));
	//ADMIN URIS
	$sb->store("uris", array(
		"path" => "sb-admin",
		"template" => "Login",
		"title" => "Bridge",
		"prefix" => "core/app/views/",
		"collective" => "0"
	));
	$admin_parent = $sb->insert_id;
	$sb->store("uris", array(
		"path" => "sb",
		"template" => "Starbug",
		"title" => "Core",
		"prefix" => "core/app/views/",
		"parent" => $admin_parent
	));
	$sb->store("uris", array(
		"path" => "sb/generate",
		"template" => "sb/generate",
		"title" => "Generate",
		"prefix" => "core/app/views/",
		"parent" => $admin_parent
	));
	$sb->store("uris", array(
		"path" => "api",
		"template" => "Api",
		"title" => "API",
		"prefix" => "core/app/views/",
		"collective" => 0,
		"check_path" => 0
	));
	//HOME PAGE
	$sb->store("uris", array(
		"path" => Etc::DEFAULT_PATH,
		"template" => Etc::DEFAULT_TEMPLATE,
		"title" => "Home",
		"prefix" => "app/views/",
		"collective" => 0,
		"check_path" => 0,
		"options" => serialize(array("layout" => '2-col-right'))
	));
	$sb->store("leafs", array(
		"leaf" => "text_leaf",
		"page" => "home",
		"container" => "content",
		"position" => 0
	));
	$sb->store("text_leaf", array(
		"page" => "home",
		"container" => "content",
		"position" => 0,
		"content" => "\t\t\t\t<h2>Congratulations, she rides!</h2>\n\t\t\t\t<p><strong>You&#39;ve successfully installed Starbug PHP!</strong></p>"
	));
	//404 PAGE
	$sb->store("uris", array(
		"path" => "missing",
		"template" => Etc::DEFAULT_TEMPLATE,
		"title" => "Missing",
		"prefix" => "app/views/",
		"collective" => 0,
		"check_path" => 0,
		"options" => serialize(array("layout" => '2-col-right'))
	));
	$sb->store("leafs", array(
		"leaf" => "text_leaf",
		"page" => "missing",
		"container" => "content",
		"position" => 0
	));
	$sb->store("leafs", array(
		"leaf" => "text_leaf",
		"page" => "missing",
		"container" => "sidebar",
		"position" => 0
	));
	$sb->store("text_leaf", array(
		"page" => "missing",
		"container" => "content",
		"position" => 0,
		"content" => "\t\t\t\t<h2>Oops!</h2>\n\t\t\t\t<p>The page you are looking for was not found.</p>"
	));
	$sb->store("text_leaf", array(
		"page" => "missing",
		"container" => "sidebar",
		"position" => 0,
		"content" => "\t\t\t\t<h2 class=\"box_top\">Why am I seeing this page?</h2>\n\t\t\t\t<div class=\"box\">\n\t\t\t\t\t<p>This reality is unstable, and anomalies have merged from both dimensions to cope with the paradox.</p>\n\t\t\t\t\t<p>Just kidding, you&#39;ve navigated to a location that either does not exist or is missing.</p>\n\t\t\t\t</div>"
	));
	//PRIVILIGES
	$sb->store("permits", array(
		"role" => "everyone",
		"action" => "login",
		"related_table" => P("users")
	));
	$sb->store("permits", array(
		"role" => "everyone",
		"action" => "logout",
		"related_table" => P("users")
	));
	$sb->store("permits", array(
		"role" => "collective",
		"action" => "read",
		"priv_type" => "global",
		"related_table" => P("uris")
	));
	//APPLY TAGS
	$sb->import("util/tags");
	function uri_list($uid) {
		global $sb;
		$prefix = array($uid);
		$children = $sb->query("uris", "where:parent=$uid");
		if (!empty($children)) foreach($children as $kid) $prefix = array_merge($prefix, uri_list($kid['id']));
		return $prefix;
	}
	$admin_uris = uri_list($admin_parent);
	foreach($admin_uris as $obj_id) tags::safe_tag("tags", "uris_tags", "1", $obj_id, "admin");
	
	//SUSBSCRIBE HOOKS
	$sb->import("util/subscribe");
	$sb->subscribe("header", "global", 10, "sb::load", "core/app/hooks/header");
	$sb->subscribe("footer", "global", 10, "sb::load", "core/app/hooks/footer");
	$sb->subscribe("footer", "dojo", 10, "sb::load", "core/app/hooks/dojo.footer");
?>
