<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file script/plugin.php used to manage plugins
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
$help = "Usage: plugin MODE [OPTIONS]\n";
$help .= "MODE\n";
$help .= "  -l                    - list plugins\n";
$help .= "  -a [PLUGIN] [MODEL]   - activate plugin\n";
$help .= "  -d [PLUGIN] [MODEL]   - deactivate plugin\n";
$help .= "  -i [PLUGIN]           - install plugin\n";
$help .= "  -u [PLUGIN]           - uninstall plugin\n\n";
$sb->import("util/plugins");
$what = array_shift($argv);
if ("-i" == $what) { //INSTALL PLUGIN
	include(array_shift($argv)."/activate.php");
} else if ("-a" == $what) { //ACTIVATE PLUGIN
	plugins::activate(array_shift($argv), array_shift($argv));
} else if ("-d" == $what) { //DEACTIVATE PLUGIN
	plugins::deactivate(array_shift($argv), array_shift($argv));
} else if ("-l" == $what) { //LIST PLUGINS
	$plugins = plugins::available(array_shift($argv));
	foreach($plugins as $name => $info) fwrite(STDOUT, (($info['active']) ? " a  " : "    ").$name."\n");
} else if ("-u" == $what) { //UNINSTALL PLUGIN
	include(array_shift($argv)."/deactivate.php");
} else echo $help;

?>
