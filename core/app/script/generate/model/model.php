<?php
	$params["config"] = $this->config;
	$base_model = "";
	if (!empty($schemer->options[$model_name]['base'])) $base_model = $schemer->options[$model_name]['base'];
	$generate = array("base" => "var/models/".ucwords($model_name)."Model.php");
	if (!file_exists(BASE_DIR.$app_dir."models/".ucwords($model_name).".php")) {
		$dirs = array($app_dir."models");
		$generate["model"] = $app_dir."models/".ucwords($model_name).".php";
	}
	//$template_map["base"] = array($base_model."/base", "base");
	//$template_map["model"] = array($base_model."/model", "model");
?>
