<?php
	$dirs = array("patch", "patch/app", "patch/app/migrations", "patch/app/hooks");
	if (empty($model)) {
		foreach ($schemer->tables as $name => $fields) passthru("sb generate patch $name");
	} else {
		XMLBuilder::write_model($model, $schemer->tables[$model]);
		$generate = array(
			"patch/migration.xsl" => "patch/app/migrations/".ucwords($model)."Migration.php"
		);
		if (!file_exists(BASE_DIR."/patch/app/hooks/global.migrations")) $hook = array(10 => array());
		else $hook = unserialize(file_get_contents(BASE_DIR."/patch/app/hooks/global.migrations"));
		$hook[10][] = array("handle" => "return_it", "args" => ucwords($model)."Migration.php");
	}
?>
