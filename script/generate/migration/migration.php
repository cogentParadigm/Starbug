<?php
	$migration = file_get_contents(BASE_DIR."/script/generate/migration/BlankMigration.php");
	$name = $model_name;
	if (empty($name)) {
		fwrite(STDOUT, "You must provide a name for your new migration");
	} else {
		$migration = str_replace("BlankMigration", $name, $migration);
		$file = fopen(BASE_DIR."/app/migrations/".$name.".php", "wb");
		fwrite($file, $migration);
		fclose($file);
	}
	global $sb;
	$sb->import("util/subscribe");
	$sb->subscribe("migrations", "global", 10, "return_it", $name);
?>
