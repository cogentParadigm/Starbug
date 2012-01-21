<?php
	js("starbug/grid/EnhancedGrid");
	if (!empty($query)) $query = star($query);
	else $query = array($model);
	$models = array_shift($query);
	$model = reset(explode(".", $models));
	assign("model", $model);
	$options = schema($model);
	foreach ($options as $k => $v) {
		if (!isset($query[$k]) && is_string($v)) $query[$k] = $v;
	}
	dfault($query['keywords'], $_GET['keywords']);
	foreach ($query as $k => $v) $query[$k] = $k.":".$v;
	if ((empty($uri)) && (end($request->uri) == $model)) {
		$uri = $request->path."/[action]";
	}
	$uri = str_replace('[model]', $model, $uri);
	assign("path", $uri);
?>
	<h1 class="heading"><div class="right"><? render("create-link"); ?></div><?php echo $options['label']; ?></h1>
	<? render_form("search"); ?>
	<?php
		$sb->import("util/grid");
		$grid = new grid(
			"model:$model",
			implode("  ", $query)
		);
		foreach ($options['fields'] as $name => $field) {
			if (!empty($view)) efault($field['list'], false);
			if ($options['list'] == "all") efault($field['list'], true);
			else efault($field['list'], false);
			if (!empty($field['views'])) {
				$field_views = explode(",", $field['views']);
				if (in_array($view, $field_views)) $field['list'] = true;
			}
			if (($field['display']) && ($field['list'])) $grid->add_column("$name  width:auto");
		}
		$grid->add_column("id  width:100  formatter:row_options", "Options");
		$grid->render();
		assign("model", $model);
		render("create-link");
	?>
	<script type="text/javascript">
			function row_options(data, rowIndex) {
				var text = '<a class="edit button" href="<?php echo uri(str_replace("[action]", "update", $uri)."/"); ?>'+data+'<?= $to; ?>"><img src="<?php echo uri("core/app/public/icons/file-edit.png"); ?>"/></a>';
				text += '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this item?\');"><input type="hidden" name="action[<?= $model; ?>]" value="delete"/><input type="hidden" name="<?= $model; ?>[id]"	value="'+data+'"/><button class="negative" title="delete" type="submit"><img src="<?php echo uri("core/app/public/icons/cross.png"); ?>"/></button></form>';
				return text;
			}
	</script>
