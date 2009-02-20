<?php
/**
* FILE: etc/Etc.php
* PURPOSE: This is the main configuration file
* NOTE: you should only edit this file post installation. 'See etc/install.php'
*
* This file is part of StarbugPHP
*
* StarbugPHP - web service development kit
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
class Etc {
	/* Log in details for database */
	const DB_TYPE = "";
	const DB_HOST = "";
	const DB_USERNAME = "";
	const DB_PASSWORD = "";
	const DB_NAME = "";

	/* Webmaster email */
	const WEBMASTER_EMAIL = "";
	/* Contact email */
	const CONTACT_EMAIL = "";
	/* No reply email */
	const NO_REPLY_EMAIL = "no-reply";

	/* Prefix for prefixed variables (ie. database tables) */
	const PREFIX = "";
	/* Name of website */
	const WEBSITE_NAME = "";
	/* URL of website */
	const WEBSITE_URL = "";

	/* Directories */
	const STYLESHEET_DIR = "public/stylesheets/";
	const IMG_DIR = "public/images/";

	/* Default redirection time */
	const REDIRECTION_TIME = 2;

	/* Elements table */
	const PATH_COLUMN = "path";
	const TEMPLATE_COLUMN = "template";
	const DEFAULT_TEMPLATE = "App";
	const DEFAULT_PATH = "home";

	/* Admin security */
	const DEFAULT_SECURITY = 2;
	const ADMIN_SECURITY = 3;
	const SUPER_ADMIN_SECURITY = 4;

	/* Time before a user is considered offline (Minutes*60) */
	const TIME_OUT = 900;
}
?>
