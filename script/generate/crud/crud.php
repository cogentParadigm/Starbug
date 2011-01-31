<?php
	$dirs = array("app/views/$prefix$model");
	$prefix = ($args->flag("p")) ? $args->flag("p") : "";
	$generate = array(
		"crud/create.xsl" => "app/views/$prefix$model/create.php",
		"crud/update.xsl" => "app/views/$prefix$model/update.php",
		"crud/list.xsl" => "app/views/$prefix$model/default.php",
		"crud/form.xsl" => "app/views/$prefix$model/form.php"
	);
?>
