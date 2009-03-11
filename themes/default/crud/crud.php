<?php
	$dirs = array("$model_name");
	$from_model = array("crud/gate.xsl" => "$model_name.php",
											"crud/create.xsl" => "$model_name/create.php",
											"crud/show.xsl" => "$model_name/show.php",
											"crud/update.xsl" => "$model_name/update.php",
											"crud/list.xsl" => "$model_name/list.php"
	);
	$from_form = array("crud/form.xsl" => "$model_name/form.php");
	$paths = array("path, template, security" => "'$model_name', '$template', '$security'");
?>
