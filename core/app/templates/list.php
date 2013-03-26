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
	$grid = capture("grid");
?>
	<h1 class="heading">
		<? link_to("New $options[singular_label]", $request->path."/create", "class:big right round create button"); ?>
		<?php echo $options['label']; ?>
	</h1>
	<?
		render_form(array($model."/search", "search"));
		echo $grid;
		link_to("Export CSV", "", array("href" => "javascript:window.location.href = ".$model."_grid.store.last_query.replace('json', 'csv');", "class" => "big right round button"));
		link_to("New $options[singular_label]", $request->path."/create", "class:big right round create button");
	?>
