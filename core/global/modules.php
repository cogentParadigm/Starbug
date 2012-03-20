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
function locate($file, $dir="templates/") {
	if (is_cached($dir.$file)) return json_decode(cache($dir.$file));
	else {
		$paths = array();
		$modules = config("modules");
		$theme = request("theme");
		if (!$theme) $theme = Etc::THEME;
		if (file_exists(BASE_DIR."/app/$dir$file")) $paths[] = "app/$dir$file";
		foreach ($modules as $module) if (file_exists(BASE_DIR."/modules/$module/$dir$file")) $paths[] = "modules/$module/$dir$file";
		if (file_exists(BASE_DIR."/app/themes/$theme/$dir$file")) $paths[] = "app/themes/$theme/$dir$file";
		if (file_exists(BASE_DIR."/core/app/$dir$file")) $paths[] = "core/app/$dir$file";
		cache($dir.$file, json_encode($paths));
		return $paths;
	}
}
?>
