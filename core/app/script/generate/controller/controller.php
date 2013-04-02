<?php
	$dir = "app/";
	if (!empty($args['module'])) {
		if ($args['module'] == "core") $dir = "core/app/";
		else $dir = "modules/".$args['module']."/";
	} else if (!empty($args['theme'])) {
		$dir = "app/themes/".$args['theme']."/";
	}
	$generate = array(
		"controller" => $dir."controllers/".ucwords($model)."Controller.php"
	);
?>
