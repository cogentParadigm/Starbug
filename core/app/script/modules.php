<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/modules.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$sb->import("core/lib/Module");
$next = array_shift($argv);
switch ($next) {
	case "install":
		$name = array_shift($argv);
		$module = new Module($name);
		$module->install();
		break;
}
?>
