<?php
/**
* This file runs Migrations in a given directory
*
* Starbug - PHP web service development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$thisdir = dirname(__FILE__)."/";
require_once $thisdir."../../etc/Etc.php";
require_once $thisdir."../../etc/init.php";
require_once $thisdir."../../util/Starr.php";
require_once $thisdir."../../util/Form.php";
require_once $thisdir."Migration.php";
$from = 0;
$to = 0;
$migrations = array();
//read in the list of migrations
$list = fopen($migrationdir."list", "r");
$firstline = true;
while (!feof($list)) {
	$line = trim(fgets($list));
	if ($firstline) {
		$from = split("=", $line);
		$from = $from[1];
		$to = $from;
		$firstline = false;
	} else if (!empty($line)) $migrations[] = $line;
}
fclose($list);
//determine which range of migrations to run
$migrationPath = ($argc < 2) ? sizeof($migrations) : $argv[1];
if (strpos($migrationPath, ":") === false) {
	$to = $migrationPath;
} else {
	$migrationPath = split(":", $migrationPath);
	$from = $migrationPath[0];
	$to = $migrationPath[1];
}
//now $migrations is our list of migrations, $from is where we start from (0 being from the start of the list), and $to is our last migration to run
if ((int)$from < (int)$to) {
	fwrite(STDOUT, "Migrating forwards..\n");
	for($i = (int)$from;$i<(int)$to;$i++) {
		require_once $migrationdir.$migrations[$i].".php";
		$migration = new $migrations[$i]($db);
		$migration->up();
	}
} else if ((int)$to < (int)$from) {
	fwrite(STDOUT, "Migrating backwards..\n");
	for($i = (((int)$from)-1);$i>=(int)$to;$i--) {
		require_once $migrationdir.$migrations[$i].".php";
		$migration = new $migrations[$i]($db);
		$migration->down();
	}
} else {
	fwrite(STDOUT, "Already at Migration ".$to."\n");
	exit(1);
}
//now we just need to write back the list file
$file = fopen($migrationdir."list", "w");
fwrite($file, "current=".$to."\n");
foreach ($migrations as $name) fwrite($file, $name."\n");
fclose($file);
exit(0);
?>