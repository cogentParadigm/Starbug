<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/migrations.php used to manage the migration list
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
	$what = array_shift($argv);
	if (("list" == $what) || ("-l" == $what)) {
		$i = 0;
		foreach($schemer->migrations as $m) {
			$i++;
			fwrite(STDOUT, $i." -> ".$m."\n");
		}
	}
	if (("add" == $what) || ("-a" == $what)) {
		$val = array_shift($argv);
		$schemer->add_migrations($val);
	}
	if (("remove" == $what) || ("-r" == $what)) {
		$val = array_shift($argv);
		if (is_numeric($val)) {
			$val = $schemer->migrations[$val-1];
		}
		$sb->import("util/subscribe");
		$sb->unsubscribe("migrations", "global", 10, "return_it", $val);
	}
?>
