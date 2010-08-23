<?php
	$migration = file_get_contents("app/themes/".Theme::FOLDER."/migration/BlankMigration.php");
	$name = $argv[2];
	if (empty($name)) {
		fwrite(STDOUT, "You must provide a name for your new migration");
	} else {
		$migration = str_replace("BlankMigration", $name, $migration);
		$file = fopen("etc/migrations/".$name.".php", "wb");
		fwrite($file, $migration);
		fclose($file);
	}
	$option = $sb->query("options", "select:id,value  where:name='migrations'  limit:1");
	$migrations = unserialize($option['value']);
	$id = $option['id'];
	$migrations[] = $name;
	$sb->store("options", "id:$id  value:".serialize($migrations));
?>
