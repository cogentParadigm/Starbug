<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/app/templates/list.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup templates
 * Default template implementation to display a list of data in a grid with options to edit and delete.
 *
 * Available variables:
 * - $query: a starbug style query string. eg: "users  select:first_name,last_name,id  where:memberships & 2  orderby:last_name ASC"
 * - $columns: (optional) an array of column overrides. set a column to false to hide it
 */
	js("starbug/grid/EnhancedGrid");
	if (!empty($query)) $query = star($query);
	else $query = array($model);
	$models = array_shift($query);
	$models = str_replace(",", ".", $models);
	$model = reset(explode(".", $models));

	efault($columns, array());
	$options = schema($model);
	foreach ($options['fields'] as $name => $field) {
		
		if ($options['list'] == "all") efault($field['list'], true);
		else efault($field['list'], false);

		if (!empty($field['views'])) {
			$field_views = explode(",", $field['views']);
			$field['list'] = (in_array($view, $field_views));
		}
		efault($field['width'], "auto");
		if (($field['display']) && ($field['list'])) efault($columns[$name], $field);
	}
	$columns["Options"] = "id  width:100  cellType:starbug.grid.cells.Options  options:'Edit':'".uri($request->path)."/update/%id%', 'Delete':'javascript:sb.post({\'action[$model]\':\'delete\', \'".$model."[id]\':%id%}, \'return confirm(\\\'Are you sure you want to delete this item?\\\')\');'";
	
	assign("columns", $columns);
?>
	<h1 class="heading">
		<? link_to("New $options[singular_label]", $request->path."/create", "class:big right round create button"); ?>
		<?php echo $options['label']; ?>
	</h1>
	<?
		render_form("search");
		render("grid");
		link_to("New $options[singular_label]", $request->path."/create", "class:big right round create button");
	?>
