<?php
	if (empty($action)) $action = "create";
	if (empty($form_header) && !empty($model)) {
		$form_header = 'Update '.$this->models->get($model)->singular_label;
	}
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New <?php echo $form_header; ?>"><?php echo $form_header; ?></span></strong></div>
	<div class="panel-body">
<?php
	$this->displays->render(ucwords($model)."Form", array_merge($this->request->getParameters(), array("operation" => $action, "id" => $id)));
?>
	</div>
</div>
