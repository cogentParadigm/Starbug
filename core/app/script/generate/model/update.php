<?php
	$base_model = "";
	if (!empty($schemer->options[$base_model]['base'])) $base_model = $schemer->options[$base_model]['base'];
	$generate = array("base" => "var/models/".ucwords($model_name)."Model.php");
	$template_map["base"] = array($base_model."/base", "base");
?>
