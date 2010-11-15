<?php
	if (!file_exists(BASE_DIR."/patch")) mkdir(BASE_DIR."/patch/app/migrations", 0, true);
	$data = "<models>\n";
	foreach ($schemer->tables as $name => $fields) {
		XMLBuilder::write_model($name, $fields);
		$data .= file_get_contents(BASE_DIR."/var/xml/$name.xml")."\n";
	}
	$data .= "</models>";
	$file = fopen(BASE_DIR."/var/xml/all.xml", "wb");
	fwrite($file, $data);
	fclose($file);
	$model = "all";
	$generate = array("patch/migration.xsl" => "patch/app/migrations/CoreMigration.php");
?>
