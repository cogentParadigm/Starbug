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
			if ($options['list'] == "all") efault($field['list'], true);
			else efault($field['list'], false);
			if (($field['display']) && ($field['list'])) $grid->add_column("$name  width:auto");
		}
		$grid->add_column("id  width:100  formatter:row_options", "Options");
		$grid->render();
		render("create-link");
	?>
	<script type="text/javascript">
			function row_options(data, rowIndex) {
				var text = sb.render('update-link', {'model':'users', 'id':data, 'to':'<?= $to ?>', 'path':'<?= $uri; ?>'});
				text += sb.form('delete', {'model':'users', 'users[id]':data});
				return text;
			}
	</script>
