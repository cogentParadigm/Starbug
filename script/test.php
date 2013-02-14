<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/test used to run unit tests and PHP syntax check
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
	$what = array_shift($argv);
	$sb->import("core/lib/test/Harness");
	global $harness;
	$no_errors = true;
	$up = true;
	$unit = true;
	$output = array();
	if ($what == "-u") {
		$up = false;
		$what = array_shift($argv);
	}
	if ($what == "-s") {
		exec("find ".BASE_DIR." -type f -name \*.php -exec php -l {} \;", $output);
		$what = array_shift($argv);
	} else if ($what == "-l") { //load layer
		$unit = false;
		$next = array_shift($argv);
		$harness->layer($next);
	} else if ($what == "-f") { //load fixture
		$unit = false;
		$next = array_shift($argv);
		$harness->fixture($next);
	} else {
		exec("git diff-index --name-only HEAD", $diff);
		foreach ($diff as $file) {
			$handle = fopen(BASE_DIR."/$file", "r");
			$line = fgets($handle);
			fclose($handle);
			if ((false !== strpos($file, ".php")) || (0 === strpos($line, "#!/usr/bin/php"))) exec("php -l ".BASE_DIR."/$file", $output);
		}
	}
	foreach($output as $line) {
		if (false === (strpos($line, "No syntax errors detected"))) {
			$no_errors = false;
			$filename = str_replace("Errors parsing ", "", $line);
			fwrite(STDOUT, "-----------------------------------------------------------------------\n");
			fwrite(STDOUT, $line."\n");
			exec("php -d display_errors=1 -l $filename", $err);
			foreach($err as $e) if ($e != $line) fwrite(STDOUT, $e."\n");
			fwrite(STDOUT, "-----------------------------------------------------------------------\n\n");
		}
	}
	if ($no_errors) fwrite(STDOUT, "\nNo syntax errors detected!\n\n");
	else exit(1);
	if ($unit) passthru("phpunit -c etc/phpunit.xml $what ".implode(" ", $argv));
?>
