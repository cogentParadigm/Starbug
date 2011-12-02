<?php
	$generate = array("base" => "var/models/".ucwords($model_name)."Model.php");
	if (!file_exists(BASE_DIR."/app/models/".ucwords($model_name).".php")) {
		$generate['child'] = "app/models/".ucwords($model_name).".php";
	}
?>
