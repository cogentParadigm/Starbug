<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/app/templates/grid.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 * Default template implementation to display the value of a field.
 *
 * Available variables:
 * - $options: An array of field values. Use render() to output them.
 * - $columns: The item label
 */
	js("starbug/grid/EnhancedGrid");
	$options = starr::star($attributes);
	list($models, $query) = explode("  ", $query, 2);
	$models = str_replace(",", ".", $models);
	$options['models'] = $models;
	$options['model'] = $model = reset(explode(".", $models, 2));
	$options['data-dojo-props'] = array();
	efault($options['id'], $ops['model']."_grid");
	efault($options['models'], $options['model']);
	efault($options['jsId'], $options['id']);
	efault($options['style'], "width:100%");
	efault($options['autoHeight'], "100");
	efault($options['rowsPerPage'], "100");
	efault($options['data-dojo-type'], "starbug.grid.EnhancedGrid");
	if (!empty($options['orderColumn'])) efault($options['plugins'], array("nestedSorting" => true, "dnd" => true));
	else efault($options['plugins'], array("nestedSorting" => true));
	$options['apiQuery'] = base64_encode($query);
	foreach ($options as $k => $v) {
		if (!in_array($k, array("id", "jsId", "class", "style", "data-dojo-type", "data-dojo-props"))) {
			$options['data-dojo-props'][$k] = $v;
			unset($options[$k]);
		}
	}
	$options['data-dojo-props'] = trim(str_replace('"', "'", json_encode($options['data-dojo-props'])), '{}');
		
	assign("attributes", $options);
	render("table");
	
?>
