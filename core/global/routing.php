<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/routing.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup routing
 */
/**
 * @defgroup routing
 * global functions
 * @ingroup global
 */
/**
 * get an absolute URI from a relative path
 * @ingroup routing
 * @param string $path the relative path
 * @param string $flags modification flag such as 's' for secure or 'f' for friendly. full list below:
 * 	s - secure
 * 	u - unsecure
 * 	f - friendly
 *  i - image (looks in image dirs)
 *  j - javascript (looks in js dirs)
 *  c - css stylesheet (looks in css dirs)
 * @return string the absolute path
 */
function uri($path="", $flags="") {
	if ($flags == "i") $suffix = "app/public/images/"; //images
	else if ($flags == "j") $suffix = "app/public/js/"; //javascript
	else if ($flags == "c") $suffix = "app/public/stylesheets/"; //css
	else $suffix = "";
	if (!empty($suffix) || empty($flags)) $prefix = ""; //auto
	else if ($flags == "s") $prefix = "https://".$_SERVER['HTTP_HOST']; //secure
	else if ($flags == "u") $prefix = "http://".$_SERVER['HTTP_HOST']; //unsecure
	else if ($flags == "f") $prefix = $_SERVER['HTTP_HOST']; //friendly
	return $prefix.Etc::WEBSITE_URL.$suffix.$path;
}
/**
 * request object access.
 * @ingroup routing
 */
function request() {
	global $request;
	$args = func_get_args();
	$count = count($args);
	if (empty($request)) return false;
	if ($count == 0) return $request;
	else if (($count == 1) && property_exists($request, $args[0])) return $request->$args[0];
	else return false;
}
/**
 * checks the path to see if a matching file exists
 * @return the file to be loaded
 */
function locate_view($uri, $prefix="") {
	if (!is_array($uri)) $uri = explode("/", $uri);
	$current = (empty($uri)) ? "default" : array_shift($uri);
	if (locate($prefix.$current, "views")) return locate_view($uri, $prefix.$current."/"); // directory found
	else if (locate($prefix.$current.".php", "views")) return $prefix.$current; // file found
	else if (locate($prefix."default.php", "views")) return $prefix."default";
	else return false;
}
/**
 * redirect to another page
 * @ingroup routing
 * @param string $url the url to redirect to
 * @param int $delay number of seconds to wait before redirecting (default 0)
 */
function redirect($url, $delay=0){
	if (!defined("SB_CLI")) {
		if(!headers_sent()) {
			header('location: '.$url);
			exit();
		} else {
			echo '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';", '.($delay*1000).');</script>';
			exit();
		}
	}
}
?>
