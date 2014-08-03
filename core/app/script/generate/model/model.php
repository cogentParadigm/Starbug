<?php
	$base_model = "";
	if (!empty($schemer->options[$base_model]['base'])) $base_model = $schemer->options[$base_model]['base'];
	$generate = array("base" => "var/models/".ucwords($model_name)."Model.php");
	if (!file_exists(BASE_DIR.$app_dir."models/".ucwords($model_name).".php")) {
		$dirs = array($app_dir."models");
		$generate["model"] = $app_dir."models/".ucwords($model_name).".php";
	}
	$template_map["base"] = array($base_model."/base", "base");
	$template_map["model"] = array($base_model."/model", "model");
?>
