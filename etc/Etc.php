<?php
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
	const STYLESHEET_DIR = "/public/stylesheets/";
	const IMG_DIR = "/public/images/";

	/* Default redirection time */
	const REDIRECTION_TIME = 2;

	/* Elements table */
	const PAGE_COLUMN = "name";
	const TEMPLATE_COLUMN = "template";
	const DEFAULT_TEMPLATE = "App";
	const DEFAULT_PAGE = "home";

	/* Admin security */
	const ADMIN_SECURITY = 3;
	const SUPER_ADMIN_SECURITY = 4;

	/* Time before a user is considered offline (Minutes*60) */
	const TIME_OUT = 900;
}
?>
