<?php
	$dirs = array("app/views/$model_name");
	$prefix = ($args->flag("p")) ? $args->flag("p") : "";
	$generate = array(
		"crud/create.xsl" => "app/views/$prefix$model_name/create.php",
		"crud/update.xsl" => "app/views/$prefix$model_name/update.php",
		"crud/list.xsl" => "app/views/$prefix$model_name/default.php",
		"crud/form.xsl" => "app/views/$prefix$model_name/form.php"
	);
?>
