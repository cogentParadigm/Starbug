<?php
foreach($args as $field => $category) {
	$varname = "_".$field;
	if (!$storing && !empty($fields[$field])) {
		$on_store = true;
		if (!is_array($fields[$field])) $fields[$field] = explode(",", $fields[$field]);
	} else {
		if (isset($fields[$field])) {
			$$varname = $fields[$field];
			unset($fields[$field]);
		}
		if (!$is_after_store) {
			$after_store = true;
		} else {
			$category_column_info = schema($name.".fields.".$field);
			efault($category_column_info['taxonomy'], $name."_".$field);
			$old_queue = $this->to_store;
			$this->to_store = array();
			$mentioned_tags = array();
			$remove_unmentioned_tags = false;
			foreach ($$varname as $tag) {
				if ($tag == "-~") {
					//remove all tags not mentioned
					$remove_unmentioned_tags = true;
				} else if (0 === strpos($tag, "-")) {
					//remove tag
					untag($name, $fields['id'], $field, substr($tag, 1));
					$mentioned_tags[] = substr($tag, 1);
				} else {
					//add tag
					//echo "tag('".$name."', ".$fields['id'].", '".$field."', '".$tag."')";
					tag($name, $fields['id'], $field, $tag);
					$mentioned_tags[] = $tag;
				}
			}
			if ($remove_unmentioned_tags) {
				query("terms_index")->condition(array(
					"type" => $name,
				"type_id" => $fields['id'],
				"rel" => $category_column_info['taxonomy']
				))->condition("terms_id", $mentioned_tags, "!=")->delete();
				
			}
			$this->to_store = $old_queue;
		}
	}
}
?>
