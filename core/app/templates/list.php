<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/app/templates/list.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup templates
 * a default layout wrapper for the grid template, that includes create buttons and a search form
 *
 * Available variables:
 * - $query: a starbug style query string. eg: "users  select:first_name,last_name,id  where:memberships & 2  orderby:last_name ASC"
 * - $columns: (optional) an array of column overrides. set a column to false to hide it
 * - $attributes: (optional) attributes for the table
 * - $view: (optional) view name. only show fields within this view
 */
	$options = schema($model);
	if ($dialog) {
		efault($grid_attributes, array());
		$grid_attributes['dialog'] = $model."_form";
		assign("grid_attributes", $grid_attributes);
	}
	$grid = capture("grid");
?>
	<h1 class="heading"><?php echo $options['label']; ?></h1>
	<?
		render(array($model."/admin-toolbar", "admin-toolbar"));
		echo $grid;
	?>
<?php if ($dialog) { ?>
<div id="<?php echo $model; ?>_dialog" data-dojo-type="starbug/form/Dialog" data-dojo-id="<?php echo $model; ?>_form" data-dojo-props="url:'<?php echo $request->path."/"; ?>', callback:function(){<?php echo $model; ?>_grid.refresh()}"></div>
<?php } ?>