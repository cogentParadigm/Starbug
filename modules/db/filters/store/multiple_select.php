<?php
foreach($args as $field => $category) {
	if (is_array($fields[$field])) {
		$category_column_info = schema($name.".fields.".$field);
		efault($category_column_info['taxonomy'], $name."_".$field);
		if (!empty($fields['id'])) {
			remove($category_column_info['taxonomy'], $name.".id=".$fields['id']);
			$old_queue = $this->to_store;
			$this->to_store = array();
			foreach ($fields[$field] as $ref_id) store($category_column_info['taxonomy'], array($name."_id" => $fields['id'], $category_column_info['type']."_id" => $ref_id));
			$this->to_store = $old_queue;
		} else {
			foreach ($fields[$field] as $ref_id) queue($category_column_info['taxonomy'], array($category_column_info['type']."_id" => $ref_id));
		}
	}
	unset($fields[$field]);
}
?>
