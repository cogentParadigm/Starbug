<?php
/**
* FILE: etc/Etc.php
* PURPOSE: This is the main configuration file
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
class Etc {
	/* Log in details for database */
	const DB_TYPE = "mysql";
	const DB_HOST = "localhost";
	const DB_USERNAME = "root";
	const DB_PASSWORD = "";
	const DB_NAME = "test";

	/* Webmaster email */
	const WEBMASTER_EMAIL = "";
	/* Contact email */
	const CONTACT_EMAIL = "";
	/* No reply email */
	const NO_REPLY_EMAIL = "no-reply";

	/* Prefix for prefixed variables (ie. database tables) */
	const PREFIX = "sb_";
	/* Name of website */
	const WEBSITE_NAME = "Starbug";
	/* Tagline Description */
	const TAGLINE = "Fresh XHTML and CSS, just like mom used to serve!";
	/* URL of website */
	const WEBSITE_URL = "http://localhost/";

	/* Directories */
	const STYLESHEET_DIR = "app/public/stylesheets/";
	const IMG_DIR = "app/public/images/";

	/* Default redirection time */
	const REDIRECTION_TIME = 2;

	/* path defaults */
	const DEFAULT_TEMPLATE = "templates/Page";
	const DEFAULT_PATH = "home";

	/* Time before a user is considered offline (Minutes*60) */
	const TIME_OUT = 900;
}
?>
