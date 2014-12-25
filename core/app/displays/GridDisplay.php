<?php
class GridDisplay {
	var $type = "grid";
	var $template = "grid";
	var $grid_class = "starbug/grid/PagedGrid";
	var $attributes = array();
	var $fields = array(
		"row_options" => array("field" => "id", "label" => "Options", "class" => "field-options", "plugin" => "starbug.grid.columns.options")
	);

	function init($options) {
		//set defaults
		if ($options['attributes']) $this->attributes = star($options['attributes']);
		$this->attributes['model'] = $this->model;
		$this->attributes['class'][] = "dgrid-autoheight";
		efault($this->attributes['id'], $this->model."_grid");
		efault($this->attributes['data-dojo-id'], $this->attributes['id']);
		$this->attributes['action'] = $this->name;

		//build data-dojo-props attribute
		foreach ($this->attributes as $k => $v) {
			if (!in_array($k, array("id", "class", "style", "data-dojo-type", "data-dojo-props", "data-dojo-id"))) {
				$this->attributes['data-dojo-props'][$k] = $v;
			}
		}

		//dnd
		if ($options['dnd']) $this->dnd();

	}

	function dnd() {
		$this->options['dnd'] = true;
		$this->grid_class = "starbug/grid/DnDGrid";
		$this->fields = array_merge(array('dnd' => array("field" => "id", "label" => "-", "class" => "field-drag",  "plugin" => "starbug.grid.columns.handle", "sortable" => false)), $this->fields);
	}

	function filter($field, $options, $column) {
		//if no column plugin has been set, try to auto-detect the appropriate plugin
		if (empty($options['plugin'])) {
			if ($column['input_type'] == "select") {
				$options['plugin'] = "starbug.grid.columns.select";
				if (!empty($column['references'])) $options['from'] = "'".reset(explode(" ", $column['references']))."'";
			} else if ($column['type'] == "bool") {
				$options['plugin'] = "starbug.grid.columns.select";
				$options['options'] = "{1:'Yes', 0:'No'}";
			} else if ($column['type'] == "terms") {
				$options['plugin'] = "starbug.grid.columns.terms";
				$options['taxonomy'] = "'".$column['taxonomy']."'";
			}
		}
		return $options;
	}

	function column_attributes($field, $options) {
		if (empty($options["field"])) $options["field"] = $field;
		$options['data-dgrid-column'] = array();
		if (empty($options['plugin']) && !isset($options['readonly'])) {
			efault($options['editor'], "'text'");
			efault($options['editOn'], "'dblclick'");
		}
		foreach ($options as $k => $v) {
			if (!in_array($k, array("id", "class", "style", "label", "data-dgrid-column", "plugin")) && $v !== "") {
				if ($k == "model" || $k == "field" || ($k == "default" && !is_numeric($v))) $v = "'".$v."'";
				else if ($v === false) $v = "false";
				$options['data-dgrid-column'][] = $k.":".$v;
			}
		}
		if (isset($options['editor'])) {
			//$options['data-dgrid-column'] = "dgrid.editor(".$options['data-dgrid-column'].", ".$options['editor'].", 'dblclick')";
		}
		$options['data-dgrid-column'] = '{'.implode(', ', $options['data-dgrid-column']).'}';
		if (isset($options['plugin']) && !isset($options['readonly'])) {
			js(str_replace(".", "/", $options['plugin']));
			$options['data-dgrid-column'] = $options['plugin']."(".$options['data-dgrid-column'].")";
		}

		return $options;
	}

	function query() {
		//defer query responsibilities to dgrid
	}

	function before_render() {
		js($this->grid_class);
		$this->attributes['data-dojo-type'] = $this->grid_class;
		//convert from array to string
		$this->attributes['data-dojo-props'] = trim(str_replace('"', "'", json_encode($this->attributes['data-dojo-props'])), '{}');
		//add query params
		$params = array_merge($_GET, $this->options);
		foreach ($params as $key => $value) if (is_array($value)) unset($params[$key]);
		if (!empty($params)) {
			$this->attributes['data-dojo-props'] .= ', query: {';
			foreach ($params as $k => $v) $this->attributes['data-dojo-props'] .= $k.":'".$v."', ";
			$this->attributes['data-dojo-props'] = rtrim($this->attributes['data-dojo-props'], ', ').'}';
		}
	}
}
?>
