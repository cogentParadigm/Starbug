<?php
	if (empty($action)) $action = "create";
	if (empty($form_header) && !empty($model)) {
		$form_header = 'Update '.$sb->models->get($model)->singular_label;
	}
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New <?php echo $form_header; ?>"><?php echo $form_header; ?></span></strong></div>
	<div class="panel-body">
<?php
	$this->render_display(ucwords($model)."Form", array_merge($_GET, array("operation" => $action, "id" => $id)));
?>
	</div>
</div>
