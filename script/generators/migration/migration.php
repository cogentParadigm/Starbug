<?php
	$migration = file_get_contents(BASE_DIR."/script/generators/migration/BlankMigration.php");
	$name = $argv[2];
	if (empty($name)) {
		fwrite(STDOUT, "You must provide a name for your new migration");
	} else {
		$migration = str_replace("BlankMigration", $name, $migration);
		$file = fopen(BASE_DIR."/app/migrations/".$name.".php", "wb");
		fwrite($file, $migration);
		fclose($file);
	}
	global $sb;
	$sb->subscribe("migrations", "global", 10, "return_it", $name);
?>
