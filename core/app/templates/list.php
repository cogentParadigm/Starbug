<?php
	$options = schema($model);
	if ((empty($uri)) && (end($request->uri) == $model)) {
		$uri = $request->path."/[action]";
	}
	$uri = str_replace('[model]', $model, $uri);
	efault($links, true);
?>
	<?php if ($links) { ?>
		<a class="big right round create button" style="margin-top:0" href="<?php echo uri(str_replace("[action]", "create", $uri)); ?>">New <?php echo $options['singular_label']; ?></a>
	<?php } ?>
	<h1><?php echo $options['label']; ?></h1>
	<?php render_form("search"); ?>
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
		$grid->add_column("id  width:60  formatter:row_options", "Options");
		$grid->render();
	?>
	<?php if ($links) { ?>
		<a class="big left round create button" href="<?php echo uri(str_replace("[action]", "create", $uri)); ?>">New <?php echo $options['singular_label']; ?></a>
	<?php } ?>
	<script type="text/javascript">
			function row_options(data, rowIndex) {
				var text = '<a class="edit button" href="<?php echo uri(str_replace("[action]", "update", $uri)."/"); ?>'+data+'<?= $to; ?>"><img src="<?php echo uri("core/app/public/icons/file-edit.png"); ?>"/></a>';
				text += '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this item?\');"><input type="hidden" name="action[<?php echo $model; ?>]" value="delete"/><input type="hidden" name="<?php echo $model; ?>[id]" value="'+data+'"/><button class="negative" title="delete"><img src="<?php echo uri("core/app/public/icons/cross.png"); ?>"/></button></form>';
				return text;
			}
	</script>
