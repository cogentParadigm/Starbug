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
	fwrite(STDOUT, "\nWelcom to the StarbugPHP Installer\nPlease enter the following information:\n\nDatabase type:");
	$dbtype = fgets(STDIN);
	fwrite(STDOUT, "Database host:");
	$dbhost = fgets(STDIN);
	fwrite(STDOUT, "Database username:");
	$dbuser = fgets(STDIN);
	fwrite(STDOUT, "Database password:");
	$dbpass = fgets(STDIN);
	fwrite(STDOUT, "Database name:");
	$dbname = fgets(STDIN);
	fwrite(STDOUT, "Site prefix:");
	$prefix = fgets(STDIN);
	fwrite(STDOUT, "Website name:");
	$sitename = fgets(STDIN);
	fwrite(STDOUT, "Website URL:");
	$siteurl = fgets(STDIN);
	fwrite(STDOUT, "Super Admin first name:");
	$admin_first = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "Super Admin last name:");
	$admin_last = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "Super Admin email:");
	$admin_email = str_replace("\n", "", fgets(STDIN));
	fwrite(STDOUT, "Super Admin password:");
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
	$data .= "\tconst WEBSITE_NAME = \"$sitename\";\n";
	$data .= "\t/* URL of website */\n";
	$data .= "\tconst WEBSITE_URL = \"$siteurl\";\n\n";
	$data .= "\t/* Directories */\n";
	$data .= "\tconst STYLESHEET_DIR = \"app/public/stylesheets/\";\n";
	$data .= "\tconst IMG_DIR = \"app/public/images/\";\n\n";
	$data .= "\t/* Default redirection time */\n";
	$data .= "\tconst REDIRECTION_TIME = 2;\n\n";
	$data .= "\t/* Elements table */\n\tconst PATH_COLUMN = \"path\";\n\tconst TEMPLATE_COLUMN = \"template\";\n\tconst DEFAULT_TEMPLATE = \"App\";\n\tconst DEFAULT_PATH = \"home\";\n\n";
	$data .= "\t/* Time before a user is considered offline (Minutes*60) */\n\tconst TIME_OUT = 900;\n}\n?>\n";
	$data = str_replace("\n\";", "\";", $data);
	$file = fopen("etc/Etc.php", "wb");
	fwrite($file, $data);
	fclose($file);

	//INIT TABLES
	include("etc/Etc.php");
	include("etc/init.php");
	include("core/db/Schemer.php");
	$sb->import("util/tags");
	$sb->db->Execute("DROP TABLE IF EXISTS `".P('permits')."`");
	$sb->db->Execute("CREATE TABLE `".P("permits")."` (id int not null AUTO_INCREMENT, role varchar(30) not null, who int not null default 0, action varchar(100) not null, status int not null default '0', priv_type varchar(30) not null default 'table', related_table varchar(100) not null, related_id int not null default '0', PRIMARY KEY (`id`) )");
	$sb->db->Execute("DROP TABLE IF EXISTS `".P('system_tags')."`");
	$sb->db->Execute("CREATE TABLE `".P("system_tags")."` (id int not null AUTO_INCREMENT, tag varchar(30) not null default '', raw_tag varchar(50) not null default '', PRIMARY KEY (`id`) )");
	$sb->db->Execute("DROP TABLE IF EXISTS `".P("uris_tags")."`");
	$sb->db->Execute("CREATE TABLE `".P("uris_tags")."` (tag_id int not null default '0', tagger_id int not null default '0', object_id int not null default '0', tagged_on datetime not null default '0000-00-00 00:00:00', PRIMARY KEY  (`tag_id`,`tagger_id`,`object_id`), KEY `tag_id_index` (`tag_id`), KEY `tagger_id_index` (`tagger_id`), KEY `object_id_index` (`object_id`) )");
	$schemer = new Schemer($sb->db);
	$schemer->create("uris");
	$schemer->create("users");

	//INSERT RECORDS
	$schemer->insert("users", "first_name, last_name, email, password, memberships", "'$admin_first', '$admin_last', '$admin_email', '$admin_pass', '1'");
	$schemer->insert("uris", "path, template, prefix, collective", "'sb-admin', 'Starbug', 'core/app/nouns/', '0'");
	$admin_parent = $schemer->db->Insert_ID();
	$schemer->insert("uris", "path, template, prefix, parent", "'sb', 'Starbug', 'core/app/nouns/', '$admin_parent'");
	$schemer->insert("uris", "path, template, prefix, parent", "'sb/generate', 'sb/generate', 'core/app/nouns/', '$admin_parent'");
	//PRIVILIGES
	$schemer->insert("permits", "role, action, related_table", "'everyone', 'login', '".P('users')."'");
	$schemer->insert("permits", "role, action, related_table", "'everyone', 'logout', '".P('users')."'");
	$schemer->insert("permits", "role, action, priv_type, related_table", "'collective', 'read', 'global', '".P('uris')."'");
	//APPLY TAGS
	function uri_list($uid) {
		$prefix = array($uid);
		$children = $sb->get("uris")->get("*", "parent=$uid")->GetRows();
		if (!empty($children)) foreach($children as $kid) $prefix = array_merge($prefix, uri_list($kid['id']));
		return $prefix;
	}
	$admin_uris = uri_list($admin_parent);
	foreach($admin_uris as $obj_id) tags::safe_tag("system_tags", "uris_tags", "1", $obj_id, "admin");
	
	//SET FILE PERMISSIONS
	exec("chmod a+x script/generate");
	exec("chmod ug+s script/cgenerate");
	exec("mkdir app/nouns var/schema/.temp var/hooks app/public/uploads app/public/thumbnails");
	exec("chmod -R a+w var app/public/uploads app/public/thumbnails");
	
	//SUSBSCRIBE HOOKS
	$sb->subscribe("header", "global", 10, "include", "core/app/hooks/header.php");
?>
