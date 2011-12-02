<?php
	$options = schema($model);
	assign("action", "create");
	assign("url", uri($uri));
	assign("fields", $options['fields']);
?>
	<h1>New <?php echo $options['singular_label']; ?></h1>
	<?php render("form"); ?>
