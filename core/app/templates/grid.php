<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/app/templates/grid.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup templates
 * Default template implementation to display a list of data in a grid with options to edit and delete.
 *
 * Available variables:
 * - $query: a starbug style query string. eg: "users  select:first_name,last_name,id  where:memberships & 2  orderby:last_name ASC"
 * - $columns: (optional) an array of column overrides. set a column to false to hide it
 * - $attributes: (optional) attributes for the table
 * - $view: (optional) view name. only show fields within this view
 */
	js("starbug/grid/Grid");
	$attributes = star($grid_attributes);

	//set up default attributes
	$attributes['model'] = $model;
	$attributes['data-dojo-props'] = array();
	efault($attributes['id'], $attributes['model']."_grid");
	efault($attributes['data-dojo-id'], $attributes['id']);
	efault($attributes['style'], "width:100%;height:615px");
	efault($attributes['data-dojo-type'], "starbug.grid.Grid");
	if ($query) {
		$params = star($query);
		$query = array_shift($params);
		$attributes['action'] = $query;
		$params = array_merge($_GET, $params);
	}
	
	//build data-dojo-props attribute
	foreach ($attributes as $k => $v) {
		if (!in_array($k, array("id", "class", "style", "data-dojo-type", "data-dojo-props", "data-dojo-id"))) {
			$attributes['data-dojo-props'][$k] = $v;
			unset($attributes[$k]);
		}
	}

	$attributes['data-dojo-props'] = trim(str_replace('"', "'", json_encode($attributes['data-dojo-props'])), '{}');
	
	if (!empty($params)) {
		$attributes['data-dojo-props'] .= ', query: {';
		foreach ($params as $k => $v) $attributes['data-dojo-props'] .= $k.":'".$v."', ";
		$attributes['data-dojo-props'] = rtrim($attributes['data-dojo-props'], ', ').'}';
	}
	
	//prepare columns
	efault($columns, array());
	$ordered_columns = array();
	$options = schema($model);
	foreach ($options['fields'] as $name => $field) {
		$merge = array();
		$field['field'] = "'".$name."'";
		$name = (empty($field['label'])) ? ucwords(str_replace('_',' ',$name)) : $field['label'];
		
		if ($options['list'] == "all") efault($field['list'], true);
		else efault($field['list'], false);

		if (!empty($field['views'])) {
			$field_views = explode(",", $field['views']);
			$field['list'] = (in_array($view, $field_views));
		}
		
		if ($field['input_type'] == "select") {
			$merge['plugin'] = "starbug.grid.columns.select";
			if (!empty($field['filters']['references'])) $merge['from'] = "'".reset(explode(" ", $field['filters']['references']))."'";
		} else if ($field['type'] == "bool") {
			$merge['plugin'] = "starbug.grid.columns.select";
			$merge['options'] = "{0:'Yes', 1:'No'}";
		}
		
		if ($field['list'] || isset($columns[$name])) {
			if (false !== $columns[$name]) {
				foreach (array('filters', 'display', $field['type'], $field['input_type'], 'type', 'input_type', 'list', "options") as $remove) unset($field[$remove]);
				$ordered_columns[$name] = empty($columns[$name]) ? $field : star($columns[$name]);
				foreach ($merge as $k => $v) if (empty($ordered_columns[$name][$k])) $ordered_columns[$name][$k] = $v;
			}
		}
		unset($columns[$name]);
	}
	$ordered_columns = array_merge($ordered_columns, $columns);
	efault($ordered_columns['Options'], "field:'id'  class:field-options  plugin:starbug.grid.columns.options");
	
	//build data-dgrid-column attributes
	foreach ($ordered_columns as $key => $value) {
		$value = star($value);
		$props = array();
		foreach ($value as $k => $v) {
			if (!in_array($k, array("id", "class", "style", "label"))) {
				$props[$k] = $v;
				unset($value[$k]);
			}
		}
		$value['data-dgrid-column'] = array();
		foreach ($props as $k => $v) {
			$value['data-dgrid-column'][] = "$k:$v";
		}
		$value['data-dgrid-column'] = '{'.implode(', ', $value['data-dgrid-column']).'}';
		if (isset($props['plugin'])) {
			js(str_replace(".", "/", $props['plugin']));
			$value['data-dgrid-column'] = $props['plugin']."(".$value['data-dgrid-column'].")";
		}
		if (empty($props['plugin'])) efault($props['editor'], "'text'");
		if (isset($props['editor'])) {
			$value['data-dgrid-column'] = "dgrid.editor(".$value['data-dgrid-column'].", ".$props['editor'].", 'dblclick')";
		}
		$ordered_columns[$key] = $value;
	}
	
	//render table
	assign("attributes", $attributes);
	assign("columns", $ordered_columns);
	render("table");
	
?>
