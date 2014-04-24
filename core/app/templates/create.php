<?php
	$options = schema($model);
	efault($form_header, 'New '.$options['singular_label']);
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New <?php echo $form_header; ?>"><?php echo $form_header; ?></span></strong></div>
	<div class="panel-body">
<?php
	assign("action", "create");
	assign("url", (empty($uri) ? "" : uri($uri)));
	assign("fields", $options['fields']);
	if (!empty($form)) render_form($form);
	else render("form");
?>
	</div>
</div>