<?php
	$containers = array(array("region" => "content", "position" => 1, "content" => "", "type" => "text"));
	$item_id = $this->get("id");
	if (!empty($item_id)) {
		$containers = query("blocks")->condition("uris_id", $this->get("uris_id"))->sort("position")->all();
	} else if (!empty($_POST[$this->model]['blocks'])) {
		$containers = array();
		foreach ($_POST[$this->model]['blocks'] as $key => $content) {
			list($region, $position) = explode("-", $key);
			$containers[] = array("region" => $region, "position" => $position, "content" => $content, "type" => "text");
		}
	}
	$field['nolabel'] = true;
	$field['class'] = "rich-text";
	$field['style'] = "width:100%;height:100px";
	$field['containers'] = $containers;
?>
