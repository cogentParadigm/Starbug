<?php
	$containers = array(array("region" => "content", "position" => 1, "content" => "", "type" => "text"));
	if (!empty($this->get("id"))) {
		$containers = query("blocks")->condition("uris_id", $this->get("id"))->sort("position")->all();
	}
	$field['nolabel'] = true;
	$field['class'] = "rich-text";
	$field['style'] = "width:100%;height:100px";
	$field['containers'] = $containers;
?>
