<?php
// FILE: etc/Etc.php
/**
 * This is the project wide configuration file
 * 
 * @package StarbugPHP
 * @subpackage etc
 * @author Ali Gangji <ali@neonrain.com>
 * @copyright 2008-2010 Ali Gangji
 */
include(BASE_DIR."/etc/Host.php");
class Etc extends Host {
	/* Name of website */
	const WEBSITE_NAME = "Starbug";
	/* Tagline Description */
	const TAGLINE = "Fresh XHTML and CSS, just like mom used to serve!";

	/* Directories */
	const STYLESHEET_DIR = "app/public/stylesheets/";
	const IMG_DIR = "app/public/images/";

	/* path defaults */
	const DEFAULT_TEMPLATE = "templates/View";
	const DEFAULT_PATH = "home";
}
include(BASE_DIR."/etc/constraints.php");
?>
