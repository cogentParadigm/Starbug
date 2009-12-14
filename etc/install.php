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
	fwrite(STDOUT, "\nWelcom to the StarbugPHP Installer\nPlease enter the following information:\n\nDatabase Type [mysql]:");
	$dbtype = fgets(STDIN);
	if (empty($dbtype)) $dbtype = "mysql";
	fwrite(STDOUT, "Database Host [localhost]:");
	$dbhost = fgets(STDIN);
	if (empty($dbhost)) $dbhost = "localhost";
	fwrite(STDOUT, "Database Name:");
	$dbname = fgets(STDIN);
	fwrite(STDOUT, "Database User:");
	$dbuser = fgets(STDIN);
	fwrite(STDOUT, "Database Password:");
	$dbpass = fgets(STDIN);
	fwrite(STDOUT, "Site Prefix:");
	$prefix = fgets(STDIN);
	fwrite(STDOUT, "Website URL:");
	$siteurl = fgets(STDIN);
	fwrite(STDOUT, "Super Admin Email:");
	$admin_email = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "Super Admin Password:");
	$admin_pass = md5(str_replace("\n", "", fgets(STDIN)));

	//WRITE CONFIG FILE
	$data = "<?php\n/**\n* FILE: etc/Etc.php\n* PURPOSE: This is the main configuration file\n*\n* This file is part of StarbugPHP\n*\n* StarbugPHP - website development kit\n* Copyright (C) 2008-2009 Ali Gangji\n*\n* StarbugPHP is free software: you can redistribute it and/or modify\n* it under the terms of the GNU General Public License as published by\n* the Free Software Foundation, either version 3 of the License, or\n* (at your option) any later version.\n*\n* StarbugPHP is distributed in the hope that it will be useful,\n* but WITHOUT ANY WARRANTY; without even the implied warranty of\n* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n* GNU General Public License for more details.\n*\n* You should have received a copy of the GNU General Public License\n* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.\n*/\nclass Etc {\n\t/* Log in details for database */\n";
	$data .= "\tconst DB_TYPE = \"$dbtype\";\n";
	$data .= "\tconst DB_HOST = \"$dbhost\";\n";
	$data .= "\tconst DB_USERNAME = \"$dbuser\";\n";
	$data .= "\tconst DB_PASSWORD = \"$dbpass\";\n";
	$data .= "\tconst DB_NAME = \"$dbname\";\n\n";
	$data .= "\t/* Webmaster email */\n";
	$data .= "\tconst WEBMASTER_EMAIL = \"$admin_email\";\n\t/* Contact email */\n\tconst CONTACT_EMAIL = \"\";\n\t/* No reply email */\n\tconst NO_REPLY_EMAIL = \"no-reply\";\n\n";
	$data .= "\t/* Prefix for prefixed variables (ie. database tables) */\n";
	$data .= "\tconst PREFIX = \"$prefix\";\n";
	$data .= "\t/* Name of website */\n";
	$data .= "\tconst WEBSITE_NAME = \"Starbug\";\n";
	$data .= "\t/* Tagline Description */\n";
	$data .= "\tconst TAGLINE = \"Fresh XHTML and CSS, just like mom used to serve!\";\n";
	$data .= "\t/* URL of website */\n";
	$data .= "\tconst WEBSITE_URL = \"$siteurl\";\n\n";
	$data .= "\t/* Directories */\n";
	$data .= "\tconst STYLESHEET_DIR = \"app/public/stylesheets/\";\n";
	$data .= "\tconst IMG_DIR = \"app/public/images/\";\n\n";
	$data .= "\t/* Default redirection time */\n";
	$data .= "\tconst REDIRECTION_TIME = 2;\n\n";
	$data .= "\t/* Elements table */\n\tconst PATH_COLUMN = \"path\";\n\tconst TEMPLATE_COLUMN = \"template\";\n\tconst DEFAULT_TEMPLATE = \"templates/Page\";\n\tconst DEFAULT_PATH = \"home\";\n\n";
	$data .= "\t/* Time before a user is considered offline (Minutes*60) */\n\tconst TIME_OUT = 900;\n}\n?>\n";
	$data = str_replace("\n\";", "\";", $data);
	$file = fopen("etc/Etc.php", "wb");
	fwrite($file, $data);
	fclose($file);
	
	//CREATE FOLDERS & SET FILE PERMISSIONS
	exec("chmod a+x script/generate");
	exec("chmod ug+s script/cgenerate");
	exec("mkdir var var/schema var/schema/.temp var/schema/.info var/hooks app/public app/public/uploads app/public/thumbnails");
	exec("chmod -R a+w var app/public/uploads app/public/thumbnails");

	//INIT TABLES
	include("etc/Etc.php");
	include("etc/init.php");
	include("core/db/Schemer.php");
	$sb->import("util/tags");
	$sb->db->Execute("DROP TABLE IF EXISTS `".P('permits')."`");
	$sb->db->Execute("CREATE TABLE `".P("permits")."` (id int not null AUTO_INCREMENT, role varchar(30) not null, who int not null default 0, action varchar(100) not null, status int not null default '4', priv_type varchar(30) not null default 'table', related_table varchar(100) not null, related_id int not null default '0', PRIMARY KEY (`id`) )");
	$sb->db->Execute("DROP TABLE IF EXISTS `".P('system_tags')."`");
	$sb->db->Execute("CREATE TABLE `".P("system_tags")."` (id int not null AUTO_INCREMENT, tag varchar(30) not null default '', raw_tag varchar(50) not null default '', PRIMARY KEY (`id`) )");
	$sb->db->Execute("DROP TABLE IF EXISTS `".P("uris_tags")."`");
	$sb->db->Execute("CREATE TABLE `".P("uris_tags")."` (tag_id int not null default '0', tagger_id int not null default '0', object_id int not null default '0', tagged_on datetime not null default '0000-00-00 00:00:00', PRIMARY KEY  (`tag_id`,`tagger_id`,`object_id`), KEY `tag_id_index` (`tag_id`), KEY `tagger_id_index` (`tagger_id`), KEY `object_id_index` (`object_id`) )");
	$schemer = new Schemer($sb->db);
	$uris = array(
		"path" => array("type" => "string", "length" => "64"),
		"template" => array("type" => "string", "length" => "64"),
		"importance" => array("type" => "int", "default" => "0", "input_type" => "select", "range" => "0:10"),
		"check_path" => array("type" => "bool", "default" => "1"),
		"prefix" => array("type" => "string", "length" => "128", "default" => "app/nouns/"),
		"parent" => array("type" => "int", "default" => "0")
	);
	$uris_info = array(
		"label" => "%path%",
		"relations" => array(
			"system_tags" => array("type" => "many", "hook" => "object_id", "lookup" => "uris_tags", "ref" => "tag_id")
		)
	);
	$users = array(
		"email" => array("type" => "string", "length" => "128", "unique" => "true"),
		"password" => array("type" => "password"),
		"memberships" => array("type" => "int")
	);
	$users_info = array("label" => "%email%");
	$pages = array(
		"title" => array("type" => "string", "length" => "128"),
		"created" => array("type" => "timestamp"),
		"name" => array("type" => "string", "length" => "64", "unique" => "true"),
		"sort_order" => array("type" => "int", "default" => "0"),
		"modified" => array("type" => "timestamp"),
		"layout" => array("type" => "string")
	);
	$pages_info = array("label" => "%title%");
	$leafs = array(
		"leaf" => array("type" => "string", "length" => "128"),
		"page" => array("type" => "string", "length" => "64"),
		"container" => array("type" => "string", "length" => "32"),
		"position" => array("type" => "int")
	);
	$leafs_info = array("label" => "%leaf%");
	$text_leaf = array(
		"page" => array("type" => "string", "length" => "64"),
		"container" => array("type" => "string", "length" => "32"),
		"position" => array("type" => "int"),
		"content" => array("type" => "text", "length" => "5000")
	);
	$text_leaf_info = array("label" => "%page% %container% %position%");
	$writes = array(
		"uris" => $uris, "users" => $users, "pages" => $pages, "leafs" => $leafs, "text_leaf" => $text_leaf,
		".info/uris" => $uris_info, ".info/users" => $users_info, ".info/pages" => $pages_info, ".info/leafs" => $leafs_info, ".info/text_leaf" => $text_leaf_info
	);
	foreach($writes as $loc => $arr) {
		$file = fopen("var/schema/$loc", "wb");
		fwrite($file, serialize($arr));
		fclose($file);
	}
	$schemer->create("uris");
	$schemer->create("users");
	$schemer->create("pages");
	$schemer->create("leafs", false, false);
	$schemer->create("text_leaf", false, false);

	//INSERT RECORDS
	//ADMIN USER
	$schemer->insert("users", "email, password, memberships", "'$admin_email', '$admin_pass', '1'");
	//ADMIN URIS
	$schemer->insert("uris", "path, template, prefix, collective", "'sb-admin', 'Starbug', 'core/app/nouns/', '0'");
	$admin_parent = $schemer->db->Insert_ID();
	$schemer->insert("uris", "path, template, prefix, parent", "'sb', 'Starbug', 'core/app/nouns/', '$admin_parent'");
	$schemer->insert("uris", "path, template, prefix, parent", "'sb/generate', 'sb/generate', 'core/app/nouns/', '$admin_parent'");
	$schemer->insert("uris", "path, template, prefix, parent", "'sb/xhr', 'Xhr', 'core/app/nouns/', '$admin_parent'");
	$schemer->insert("uris", "path, template, prefix, collective, check_path", "'api', 'Api', 'core/app/nouns/', '0', '0'");
	//HOME PAGE
	$schemer->insert("uris", "path, template, collective, check_path", "'".Etc::DEFAULT_PATH."', '".Etc::DEFAULT_TEMPLATE."', '0', '0'");
	$schemer->insert("pages", "title, created, modified, name, layout", "'Home', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', '".Etc::DEFAULT_PATH."', '2-col-right'");
	$schemer->insert("leafs", "leaf, page, container, position", "'text_leaf', 'home', 'content', '0'");
	$schemer->insert("text_leaf", "page, container, position, content", "'home', 'content', '0', '\t\t\t\t<h2>Congratulations, she rides!</h2>\n\t\t\t\t<p><strong>You\'ve successfully installed Starbug PHP!</strong></p>'");
	//404 PAGE
	$schemer->insert("pages", "title, created, modified, name, layout", "'Missing', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', 'missing', '2-col-right'");
	$schemer->insert("leafs", "leaf, page, container, position", "'text_leaf', 'missing', 'content', '0'");
	$schemer->insert("leafs", "leaf, page, container, position", "'text_leaf', 'missing', 'sidebar', '0'");
	$schemer->insert("text_leaf", "page, container, position, content", "'missing', 'content', '0', '\t\t\t\t<h2>Oops!</h2>\n\t\t\t\t<p>The page you are looking for was not found.</p>'");
	$schemer->insert("text_leaf", "page, container, position, content", "'missing', 'sidebar', '0', '\t\t\t\t<h2 class=\"box_top\">Why am I seeing this page?</h2>\n\t\t\t\t<div class=\"box\">\n\t\t\t\t\t<p>This reality is unstable, and anomalies have merged from both dimensions to cope with the paradox.</p>\n\t\t\t\t\t<p>Just kidding, you\'ve navigated to a location that either does not exist or is missing.</p>\n\t\t\t\t</div>'");
	//PRIVILIGES
	$schemer->insert("permits", "role, action, related_table", "'everyone', 'login', '".P('users')."'");
	$schemer->insert("permits", "role, action, related_table", "'everyone', 'logout', '".P('users')."'");
	$schemer->insert("permits", "role, action, priv_type, related_table", "'collective', 'read', 'global', '".P('uris')."'");
	//APPLY TAGS
	function uri_list($uid) {
		global $sb;
		$prefix = array($uid);
		$children = $sb->query("uris", "where:parent=$uid");
		if (!empty($children)) foreach($children as $kid) $prefix = array_merge($prefix, uri_list($kid['id']));
		return $prefix;
	}
	$admin_uris = uri_list($admin_parent);
	foreach($admin_uris as $obj_id) tags::safe_tag("system_tags", "uris_tags", "1", $obj_id, "admin");
	
	//SUSBSCRIBE HOOKS
	$sb->subscribe("header", "global", 10, "sb::load", "core/app/hooks/header");
	$sb->subscribe("footer", "global", 10, "sb::load", "core/app/hooks/footer");
	$sb->subscribe("footer", "dojo", 10, "sb::load", "core/app/hooks/dojo.footer");
?>
