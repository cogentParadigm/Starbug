<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/info.php used to obtain information such as version info and command help
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$next = array_shift($argv);
efault($next, "-v");
if ("-v" == $next) {
	include(BASE_DIR."/core/version.php");
	echo "Starbug PHP v".version::starbug."\n";
}
?>
