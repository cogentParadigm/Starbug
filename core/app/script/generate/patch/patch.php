<?php
	if (!file_exists($this->base_directory."/patch")) mkdir($this->base_directory."/patch/app/migrations", 0, true);
	$data = "<models>\n";
	foreach ($this->schemer->tables as $name => $fields) {
		XMLBuilder::write_model($name, $fields);
		$data .= file_get_contents($this->base_directory."/var/xml/$name.xml")."\n";
	}
	$data .= "</models>";
	$file = fopen($this->base_directory."/var/xml/all.xml", "wb");
	fwrite($file, $data);
	fclose($file);
	$model = "all";
	$generate = array("patch/migration.xsl" => "patch/app/migrations/CoreMigration.php");
?>
