<?php
foreach($args as $field => $category) {
		if(!is_numeric($fields[$field])) {
			$errors[$field]['required'] = 'This field is required';
		} else if($fields[$field] == -1) {
			if(empty($fields[$field.'_new_category'])) {
				$errors[$field.'_new_category'][] = 'This field is required';
			} else {
				$old_queue = $this->to_store;
				$this->to_store = array();
				$category_column_info = schema($name.".fields.".$field);
				efault($category_column_info['taxonomy'], $name."_".$field);
				sb('terms','create',array(
					'term' => $fields[$field.'_new_category'],
					'taxonomy' => $category_column_info['taxonomy']
				));
				if(errors('terms[slug]')) $errors[$field.'_new_category'][] = "That $field already exists";
				else {
					$this->to_store = $old_queue;
					$fields[$field] = $_POST[$name][$field] = sb('terms', 'insert_id');
				}
			}
		}
		unset($fields[$field.'_new_category']);
}
?>
