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
