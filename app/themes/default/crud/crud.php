<?php
	$dirs = array("app/views/$model_name");
	$generate = array(
		"crud/create.xsl" => "app/views/$model_name/create.php",
		"crud/update.xsl" => "app/views/$model_name/update.php",
		"crud/list.xsl" => "app/views/$model_name/default.php",
		"crud/form.xsl" => "app/views/$model_name/form.php"
	);
?>
