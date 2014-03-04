<?php
class hook_store_category {
	function store(&$query, $key, $value, $column, $argument) {
		if (!is_numeric($value)) error("This field is required", $column);
	}
	
	function after_store(&$query, $key, $value, $column, $argument) {
		$existing = query("terms_index");
		$existing->condition("type", $query->model);
		$existing->condition("rel", $column);
		$existing->condition("type_id", $query->getId());
		$result = $existing->one();
		
		$existing->set("type", $query->model);
		$existing->set("rel", $column);
		$existing->set("type_id", $query->getId());
		$existing->set("terms_id", $value);
		if (empty($result)) {
			$existing->insert();
		} else {
			$existing->update();
		}
	}
}
/*
foreach($args as $field => $category) {
	$varname = "_".$field;
	if (!$storing) {
		if(!is_numeric($fields[$field])) {
			$errors[$field]['required'] = 'This field is required';
		} else if($fields[$field] == -1) {
			if(!empty($fields[$field.'_new_category'])) {
				$old_queue = $this->to_store;
				$this->to_store = array();
				$category_column_info = schema($name.".fields.".$field);
				efault($category_column_info['taxonomy'], $name."_".$field);
				sb('terms','create',array(
					'term' => $fields[$field.'_new_category'],
					'taxonomy' => $category_column_info['taxonomy'],
					'parent' => 0,
					'position' => ''
				));
				if(errors('terms[slug]')) $errors[$field.'_new_category'][] = "That $field already exists";
				else {
					$this->to_store = $old_queue;
					$fields[$field] = $_POST[$name][$field] = sb('terms', 'insert_id');
				}
			}
		} else {
			unset($errors[$field.'_new_category']);
		}
		unset($fields[$field.'_new_category']);
		$on_store = true;
	} else {
		if (isset($fields[$field])) {
			$$varname = $fields[$field];
			unset($fields[$field]);
		}
		if (!$after_store) {
			$after_store = true;
		} else {
			//store term
			$existing = get("terms_index", array("type" => $name, "type_id" => $fields['id'], "rel" => $field));
			$store_from = ($existing) ? array("type" => $name, "type_id" => $fields['id'], "rel" => $field) : "auto";
			$old_queue = $this->to_store;
			$this->to_store = array();
			store("terms_index", array("type" => $name, "type_id" => $fields['id'], "rel" => $field, "terms_id" => $$varname), $store_from);
			$this->to_store = $old_queue;
		}
	}
}
*/
?>
