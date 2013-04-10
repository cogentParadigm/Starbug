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
	global $request;
	if ($request) $theme = request("theme");
	//efault($theme, settings("theme"));
	if (!empty($dir)) $dir .= "/";
	$path = $dir.$file;
	$key = $theme.'_'.$path;
	if (is_cached($key)) return cache($key);
	else {
		$paths = array();
		$modules = config("modules");
		if (file_exists(BASE_DIR."/core/app/$path")) $paths[] = BASE_DIR."/core/app/$path";
		foreach ($modules as $module) if (file_exists(BASE_DIR."/modules/$module/$path")) $paths[] = BASE_DIR."/modules/$module/$path";
		if (file_exists(BASE_DIR."/app/themes/$theme/$path")) $paths[] = BASE_DIR."/app/themes/$theme/$path";
		if (file_exists(BASE_DIR."/app/$path")) $paths[] = BASE_DIR."/app/$path";
		cache($key, $paths);
		return $paths;
	}
}
/**
 * get module index
 */
function get_module_index() {
	$modules = config("modules");
	$index = array();
	foreach ($modules as $module) {
		$index[$module] = $module;
		$info = module($module);
		if (!empty($info['provides'])) {
			if (!is_array($info['provides'])) $info['provides'] = array($info['provides']);
			foreach ($info['provides'] as $provided) $index[$provided] = $module;
		}
	}
	return $index;
}

/**
 * get a controller by name
 * @param string $name the name of the controller, such as 'users'
 * @param string $type a sub type such as 'admin'
 * @return the instantiated controller
 */
function controller($name, $type="") {
	import("Controller", "core/lib");

	$class = ucwords($type).ucwords($name)."Controller";

	$last = "Controller";
	
	//get extending controllers
	$controllers = locate("$class.php", "controllers");
	$count = count($controllers);
	$search = "class $class {";
	
	//loop through found controllers
	for ($i = 0; $i < $count; $i++) {
		//get file contents
		$contents = file_get_contents($controllers[$i]);
		//make class name unique and extend the previous class
		$class = str_replace(array(BASE_DIR.'/', '/'), array('', '_'), reset(explode('/controllers/', $controllers[$i])))."__$class";
		$replace = "class $class extends $last {";
		//replace and eval
		eval('?>'.str_replace($search, $replace, $contents));
		//set $last for the next round
		$last = $class;
	}
	
	//return the base model if no others
	if ($count == 0) $class = $last;

	//instantiate save the object
	return new $class($name, $type);
}
