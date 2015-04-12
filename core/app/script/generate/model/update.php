<?php
	$base_model = "";
	if (!empty($this->schemer->options[$model_name]['base'])) $base_model = $this->schemer->options[$model_name]['base'];
	$generate = array("base" => "var/models/".ucwords($model_name)."Model.php");
	//$template_map["base"] = array($base_model."/base", "base");
?>
