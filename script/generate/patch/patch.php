<?php
	if (empty($models)) foreach ($schemer->tables as $name => $fields) passthru("sb generate patch $name");
	else {
		XMLBuilder::write_model($model, $schemer->tables[$model]);
		$dirs = array("patch", "patch/app", "patch/app/migrations");
		$generate = array(
			"patch/migration.xsl" => "patch/app/migrations/".ucwords($model)."Migration.php"
		);
?>
