<?php
	$options = schema($model);
	efault($form_header, 'New '.$options['singular_label']);
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New <?php echo $form_header; ?>"><?php echo $form_header; ?></span></strong></div>
	<div class="panel-body">
<?php
	efault($action, "create");
	$this->render_display("form", $model, "form", array_merge($_GET, array("action" => $action, "cancel_url" => $cancel_url)));
?>
	</div>
</div>
