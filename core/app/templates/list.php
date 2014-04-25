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
 * - $query: an API query string. eg: "list  groups:user" - this will call query_list of the assigned model and will pass "groups" => "user" in the second argument.
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
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="<?php echo $options['label']; ?>"><?php echo $options['label']; ?></span></strong></div>
	<div class="panel-body">
	<?
		render(array($model."/admin-toolbar", "admin-toolbar"));
		render_display("grid", $model, $query, array("attributes" => $grid_attributes));
	?>
	</div>
</div>
<?php if ($dialog) { ?>
<div id="<?php echo $model; ?>_dialog" data-dojo-type="starbug/form/Dialog" data-dojo-id="<?php echo $model; ?>_form" data-dojo-props="url:'<?php echo $request->path."/"; ?>', callback:function(){<?php echo $model; ?>_grid.refresh()}"></div>
<?php } ?>
