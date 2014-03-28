<?php
class hook_store_terms {
	function after_store(&$query, $key, $value, $column, $argument) {
		$name = $query->model;
		$id = $query->getId();
		$category_column_info = schema($name.".fields.".$column);
		efault($category_column_info['taxonomy'], $name."_".$column);
		$mentioned_tags = array();
		$remove_unmentioned_tags = false;
		if (!is_array($value)) $value = explode(",", $value);
		foreach ($value as $tag) {
			if ($tag == "-~") {
				//remove all tags not mentioned
				$remove_unmentioned_tags = true;
			} else if (0 === strpos($tag, "-")) {
				//remove tag
				untag($name, $id, $column, substr($tag, 1));
				$mentioned_tags[] = substr($tag, 1);
			} else {
				//add tag
				//echo "tag('".$name."', ".$id.", '".$column."', '".$tag."')";
				tag($name, $id, $column, $tag);
				$mentioned_tags[] = $tag;
			}
		}
		if ($remove_unmentioned_tags) {
			query("terms_index")->condition(array(
				"type" => $name,
				"type_id" => $id,
				"rel" => $category_column_info['taxonomy']
			))->condition("terms_id", $mentioned_tags, "!=")
			->condition("terms_id.slug", $mentioned_tags, "!=")
			->condition("terms_id.term", $mentioned_tags, "!=")->delete();		
		}
	}
}
?>
