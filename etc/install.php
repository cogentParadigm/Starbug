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
* StarbugPHP - meta content manager
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
	$data = "<?php\n/**\n* FILE: etc/Etc.php\n* PURPOSE: This is the main configuration file\n*\n* This file is part of StarbugPHP\n*\n* StarbugPHP - web service development kit\n* Copyright (C) 2008-2009 Ali Gangji\n*\n* StarbugPHP is free software: you can redistribute it and/or modify\n* it under the terms of the GNU General Public License as published by\n* the Free Software Foundation, either version 3 of the License, or\n* (at your option) any later version.\n*\n* StarbugPHP is distributed in the hope that it will be useful,\n* but WITHOUT ANY WARRANTY; without even the implied warranty of\n* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n* GNU General Public License for more details.\n*\n* You should have received a copy of the GNU General Public License\n* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.\n*/\nclass Etc {\n\t/* Log in details for database */\n";
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
	$data .= "\tconst STYLESHEET_DIR = \"public/stylesheets/\";\n";
	$data .= "\tconst IMG_DIR = \"public/images/\";\n\n";
	$data .= "\t/* Default redirection time */\n";
	$data .= "\tconst REDIRECTION_TIME = 2;\n\n";
	$data .= "\t/* Elements table */\n\tconst PATH_COLUMN = \"path\";\n\tconst TEMPLATE_COLUMN = \"template\";\n\tconst DEFAULT_TEMPLATE = \"App\";\n\tconst DEFAULT_PATH = \"home\";\n\n";
	$data .= "\t/* Admin security */\n\tconst DEFAULT_SECURITY = 2;\n\tconst ADMIN_SECURITY = 3;\n\tconst SUPER_ADMIN_SECURITY = 4;\n\n";
	$data .= "\t/* Time before a user is considered offline (Minutes*60) */\n\tconst TIME_OUT = 900;\n}\n?>\n";
	$data = str_replace("\n\";", "\";", $data);
	$file = fopen("etc/Etc.php", "wb");
	fwrite($file, $data);
	fclose($file);

	//INIT TABLES
	include("etc/Etc.php");
	include("etc/init.php");
	include("core/db/Schemer.php");
	$db->Execute("DROP TABLE IF EXISTS `".P('permits')."`");
	$db->Execute("CREATE TABLE `".P("permits")."` (id int not null AUTO_INCREMENT, role varchar(30) not null, who int not null default 0, action varchar(100) not null, status int not null default '0', priv_type varchar(30) not null default 'table', related_table varchar(100) not null, related_id int not null default '0', PRIMARY KEY (`id`) )");
	$schemer = new Schemer($db);
	$schemer->create("uris");
	$schemer->create("users");

	//INSERT RECORDS
	$schemer->insert("users", "first_name, last_name, email, password, memberships", "'$admin_first', '$admin_last', '$admin_email', '$admin_pass', '1'");
	$schemer->insert("uris", "path, template", "'uris', 'Starbug'");
	$schemer->insert("uris", "path, template, visible", "'uris/new', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'uris/get', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'uris/edit', 'Ajax', '0'");
	$schemer->insert("uris", "path, template", "'models', 'Starbug'");
	$schemer->insert("uris", "path, template, visible", "'models/new', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'models/get', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'models/edit', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'models/remove', 'Ajax', '0'");
	$schemer->insert("uris", "path, template", "'users', 'Starbug'");
	$schemer->insert("uris", "path, template, visible", "'users/new', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'users/get', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'users/edit', 'Ajax', '0'");
	$schemer->insert("uris", "path, template, visible", "'generate', 'Starbug', '0'");
	$schemer->insert("uris", "path, template, visible, collective", "'login', 'Starbug', '0', '0'");
	//PRIVILIGES
	$schemer->insert("permits", "role, action, related_table", "'everyone', 'login', '".P('users')."'");
	$schemer->insert("permits", "role, action, related_table", "'everyone', 'logout', '".P('users')."'");
	$schemer->insert("permits", "role, action, priv_type, related_table", "'collective', 'read', 'global', '".P('uris')."'");
	
	//SET PERMISSIONS
	exec("chmod a+x script/generate");
	exec("chmod u+s script/generate");
	exec("chmod -R a+w core/db/schema");
?>
