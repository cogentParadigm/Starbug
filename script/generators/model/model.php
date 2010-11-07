<?php
	$generate = array("model/base.xsl" => "var/models/".ucwords($model_name)."Model.php");
	if (!file_exists(BASE_DIR."/app/models/".ucwords($model_name).".php")) {
		$generate['model/model.xsl'] = "app/models/".ucwords($model_name).".php";
	}
?>
