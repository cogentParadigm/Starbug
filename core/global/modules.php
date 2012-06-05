<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/modules.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup modules
 */
/**
 * @defgroup modules
 * global functions
 * @ingroup global
 */
/**
 * utility import function
 * @ingroup modules
 * @param string $util the utility
 * @param string $module the module
 */
function import($util, $module="util") {
	global $sb;
	$sb->import($module."/".$util);
}
/**
 * get module path chain
 * @ingroup modules
 * @param string $file the filename
 * @param string $dir the directory within app/ core/app/ or module dir to look in. default is templates/
 * @TODO allow boolean return
 */
function locate($file, $dir="templates") {
	if (!empty($dir)) $dir .= "/";
	$path = $dir.$file;
	if (is_cached($path)) return cache($path);
	else {
		$paths = array();
		$modules = config("modules");
		global $request;
		if ($request) $theme = request("theme");
		efault($theme, Etc::THEME);
		if (file_exists(BASE_DIR."/core/app/$path")) $paths[] = BASE_DIR."/core/app/$path";
		foreach ($modules as $module) if (file_exists(BASE_DIR."/modules/$module/$path")) $paths[] = BASE_DIR."/modules/$module/$path";
		if (file_exists(BASE_DIR."/app/themes/$theme/$path")) $paths[] = BASE_DIR."/app/themes/$theme/$path";
		if (file_exists(BASE_DIR."/app/$path")) $paths[] = BASE_DIR."/app/$path";
		cache($path, $paths);
		return $paths;
	}
}
?>
