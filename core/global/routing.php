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
 * 		global $request;
 * is equivalent to:
 * 		$request = request();
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
	efault($prefix, request()->payload['prefix']);
	if (!is_array($uri)) $uri = explode("/", $uri);
	$current = (empty($uri)) ? "default" : array_shift($uri);
	if (file_exists($prefix.$current.".php")) return $prefix.$current.".php"; // file found
	else if (file_exists($prefix.$current)) return locate_view($uri, $prefix.$current."/"); // directory found
	else if (file_exists($prefix."default.php")) return $prefix."default.php";
	else return false;
}
/**
 * check to see if this the default path (the front page)
 * @ingroup routing
 * @return bool true if it is, false if it isn't
 */
function is_default_path() {
	global $request;
	return ($request->path == settings("default_path"));
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
