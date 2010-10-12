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
	$no_errors = true;
	if ($what == "-s") exec("find ".BASE_DIR." -type f -name \*.php -exec php -l {} \;", $output);
	else {
		exec("git diff-index --name-only HEAD", $diff);
		foreach ($diff as $file) {
			$handle = fopen(BASE_DIR."/$file", "r");
			$line = fgets($handle);
			fclose($file);
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
	if (! defined('SIMPLE_TEST')) define('SIMPLE_TEST', BASE_DIR.'/util/simpletest/');
	require_once(SIMPLE_TEST."unit_tester.php");
	require_once(SIMPLE_TEST."web_tester.php");
	require_once(BASE_DIR."/core/app/tests/views/ViewTestCase.php");
	require_once(SIMPLE_TEST."reporter.php");
	$group = new TestSuite("Models Test");
	$files = array();
	if ($handle = opendir(BASE_DIR."/app/tests/models")) while(false !== ($file = readdir($handle))) if (!is_dir(BASE_DIR."/app/tests/models/$file")) $files[$file] = $file;
	foreach($files as $file) $group->addFile(BASE_DIR."/app/tests/models/$file");
	$success1 = $group->run(new TextReporter());
	$group = new TestSuite("Views Test");
	$files = array();
	if ($handle = opendir(BASE_DIR."/app/tests/views")) while(false !== ($file = readdir($handle))) if (!is_dir(BASE_DIR."/app/tests/views/$file")) $files[$file] = $file;
	foreach($files as $file) $group->addFile(BASE_DIR."/app/tests/views/$file");
	$success2 = $group->run(new TextReporter());
	exit((($success1 && $success2) ? 0 : 1));
?>
