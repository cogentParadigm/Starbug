<?php
	$dirs = array("app/nouns/$model_name");
	$from_model = array("crud/create.xsl" => "app/nouns/$model_name/create.php",
											"crud/update.xsl" => "app/nouns/$model_name/update.php",
											"crud/list.xsl" => "app/nouns/$model_name/default.php"
	);
	$from_form = array("crud/form.xsl" => "app/nouns/$model_name/form.php");
?>
