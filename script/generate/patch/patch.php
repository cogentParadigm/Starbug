<?php
	foreach ($schemer->tables as $name => $fields) XMLBuilder::write_model($name, $fields);
	$dirs = array("patch", "patch/app", "patch/app/migrations");
	$generate = array(
		"patch/migration.xsl" => "patch/app/migrations/".ucwords($model)."Migration.php"
	);
?>
