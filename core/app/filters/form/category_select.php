<?php
	if (isset($field['multiple'])) {
		$field['multiple'] = "multiple";
		$field['name'] = $field['name']."[]";
		efault($field['size'], 5);
	}
	$value = $this->get($field['name']);
	if ((empty($value)) && (!empty($field['default']))) {
		$this->set($field['name'], $field['default']);
		unset($field['default']);
	}
	efault($field['taxonomy'], ((empty($this->model)) ? "" : $this->model."_").$field['name']);
	efault($field['parent'], 0);
	$terms = terms($field['taxonomy'], $field['parent']);
	$options = array();
	foreach ($terms as $term) $options[str_pad($term['term'], strlen($term['term'])+$term['depth'], "-", STR_PAD_LEFT)] = $term['slug'];
	if (isset($field['optional'])) array_unshift($terms, array("term" => $field['optional'], "id" => 0));
	if (isset($field['writable'])) {
		$options["Add a new ".str_replace("_", " ", $field['name']).".."] = -1;
		$field['onchange'] = "if (dojo.attr(this, 'value') == -1) dojo.style(this.id+'_new_category', 'display', 'block'); else dojo.style(this.id+'_new_category', 'display', 'none');";
	}
	assign("value", $this->get($field['name']));
	assign("options", $options);
	assign("terms", $terms);
?>
