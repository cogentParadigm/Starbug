<?php
class hook_store_blocks {
	function validate($query, $key, $value, $column, $argument) {
		$query->exclude($key);
		if ($query->mode == "insert") {
			queue("blocks", array("type" => "text",  "region" => "content",  "position" => 1, "uris_id" => "", "content" => filter_html($value['content-1'])));
		} else {
			$blocks = query("blocks")->select("blocks.*")->condition($query->model."_id", $query->fields["id"])->all();
			foreach ($blocks as $block) {
				$key = $block['region'].'-'.$block['position'];
				if (isset($value[$key])) queue("blocks", array("id" => $block['id'], "content" => filter_html($value[$key])));
			}
		}
		return $value;
	}
	function before_delete($query, $column, $argument) {
		$id = $query->getId();
		query("blocks")->condition($query->model."_id", $id)->delete();
	}
}
?>
