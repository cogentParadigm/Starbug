<?php
	$generate = array("model/base.xsl" => "core/app/models/".ucwords($model_name)."Model.php");
	if (!file_exists(BASE_DIR."/app/models/".ucwords($model_name).".php")) {
		$generate['model/model.xsl'] = "app/models/".ucwords($model_name).".php";
	}
?>
