<?php
if ((Etc::DB_NAME == "") && (!empty($_POST['configure_starbug']))) {
	$data = "<?php\n/**\n* This is the main configuration file\n*\n* This file is part of StarbugPHP\n*\n* StarbugPHP - web service development kit\n* Copyright (C) 2008-2009 Ali Gangji\n*\n* StarbugPHP is free software: you can redistribute it and/or modify\n* it under the terms of the GNU General Public License as published by\n* the Free Software Foundation, either version 3 of the License, or\n* (at your option) any later version.\n*\n* StarbugPHP is distributed in the hope that it will be useful,\n* but WITHOUT ANY WARRANTY; without even the implied warranty of\n* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n* GNU General Public License for more details.\n*\n* You should have received a copy of the GNU General Public License\n* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.\n*/\nclass Etc {\n\t/* Log in details for database */\n";
	$data .= "\tconst DB_TYPE = \"$_POST[dbtype]\";\n";
	$data .= "\tconst DB_HOST = \"$_POST[dbhost]\";\n";
	$data .= "\tconst DB_USERNAME = \"$_POST[dbuser]\";\n";
	$data .= "\tconst DB_PASSWORD = \"$_POST[dbpass]\";\n";
	$data .= "\tconst DB_NAME = \"$_POST[dbname]\";\n\n";
	$data .= "\t/* Webmaster email */\n";
	$data .= "\tconst WEBMASTER_EMAIL = \"\";\n\t/* Contact email */\n\tconst CONTACT_EMAIL = \"\";\n\t/* No reply email */\n\tconst NO_REPLY_EMAIL = \"no-reply\";\n\n";
	$data .= "\t/* Prefix for prefixed variables (ie. database tables) */\n";
	$data .= "const PREFIX = \"$_POST[prefix]\";\n";
	$data .= "\t/* Name of website */\n";
	$data .= "\tconst WEBSITE_NAME = \"$_POST[sitename]\";\n";
	$data .= "\t/* URL of website */\n";
	$data .= "\tconst WEBSITE_URL = \"$_POST[siteurl]\";\n\n";
	$data .= "\t/* Directories */\n";
	$data .= "\tconst STYLESHEET_DIR = \"public/stylesheets/\";\n";
	$data .= "\tconst IMG_DIR = \"public/images/\";\n\n";
	$data .= "\t/* Default redirection time */\n";
	$data .= "\tconst REDIRECTION_TIME = 2;\n\n";
	$data .= "\t/* Elements table */\n\tconst PATH_COLUMN = \"path\";\n\tconst TEMPLATE_COLUMN = \"template\";\n\tconst DEFAULT_TEMPLATE = \"App\";\n\tconst DEFAULT_PATH = \"home\";\n\n";
	$data .= "\t/* Admin security */\n\tconst ADMIN_SECURITY = 3;\n\tconst SUPER_ADMIN_SECURITY = 4;\n\n";
	$data .= "\t/* Time before a user is considered offline (Minutes*60) */\n\tconst TIME_OUT = 900;\n}\n?>\n";
	$file = fopen("etc/Etc.php", "wb");
	fwrite($file, $data);
	fclose($file);
	header("Location: $_POST[siteurl]");
} else if (Etc::DB_NAME != "") {
	if (($_POST['starbug_create_tables'])) {
		$uris = unserialize(file_get_contents("core/db/schema/uris"));
		$users = unserialize(file_get_contents("core/db/schema/users"));
		include("core/db/Schemer.php");
		$schemer = new Schemer($this->db);
		$schemer->create("uris", $uris);
		$schemer->create("users", $users);
		$schemer->insert("users", "first_name, last_name, email, password, security", "'$_POST[adminfirst_name]', '$_POST[adminlast_name]', '$_POST[adminemail]', '".md5($_POST['adminpass'])."', '".Etc::SUPER_ADMIN_SECURITY."'");
		$schemer->insert("uris", "path, template, security", "'uris', 'Starbug', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/new', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/get', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/edit', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'uris/add', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, security", "'models', 'Starbug', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/new', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/get', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/edit', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'models/add', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, security", "'users', 'Starbug', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/new', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/get', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/edit', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'users/add', 'Ajax', '0', '4'");
		$schemer->insert("uris", "path, template, visible, security", "'login', 'Starbug', '0', '0'");
	} else $starbug_create_tables = true;
}
?>