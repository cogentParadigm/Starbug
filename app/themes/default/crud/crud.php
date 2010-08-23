<?php
	$dirs = array("app/views/$model_name");
	$from_model = array("crud/create.xsl" => "app/views/$model_name/create.php",
											"crud/update.xsl" => "app/views/$model_name/update.php",
											"crud/list.xsl" => "app/views/$model_name/default.php"
	);
	$from_form = array("crud/form.xsl" => "app/views/$model_name/form.php");
	$paths = array("path, template, collective" => "'$model_name', '$template', '$collective'");
?>
