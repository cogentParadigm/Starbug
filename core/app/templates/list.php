<?php
	$options = schema($model);
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
			"keywords:$_GET[keywords]  search:$options[search]  select:$options[select]"
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
