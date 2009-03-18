<?php
	$dirs = array("app/nouns/$model_name");
	$from_model = array("crud/gate.xsl" => "app/nouns/$model_name.php",
											"crud/create.xsl" => "app/nouns/$model_name/create.php",
											"crud/show.xsl" => "app/nouns/$model_name/show.php",
											"crud/update.xsl" => "app/nouns/$model_name/update.php",
											"crud/list.xsl" => "app/nouns/$model_name/list.php"
	);
	$from_form = array("crud/form.xsl" => "app/nouns/$model_name/form.php");
	$paths = array("path, template, security" => "'$model_name', '$template', '$security'");
?>
