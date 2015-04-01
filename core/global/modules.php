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
