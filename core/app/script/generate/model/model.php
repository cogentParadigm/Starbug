<?php
	$generate = array("base" => "var/models/".ucwords($model_name)."Model.php");
	if (!file_exists(BASE_DIR.$app_dir."models/".ucwords($model_name).".php")) {
		$dirs = array($app_dir."models");
		$generate["model"] = $app_dir."models/".ucwords($model_name).".php";
	}
?>
