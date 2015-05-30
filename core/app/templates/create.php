<?php
	if (empty($form_header) && !empty($model)) {
		$form_header = 'New '.$this->models->get($model)->singular_label;
	}
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New <?php echo $form_header; ?>"><?php echo $form_header; ?></span></strong></div>
	<div class="panel-body">
<?php
	if (empty($action)) $action = "create";
	$this->displays->render(ucwords($model)."Form", array_merge($_GET, array("action" => $action)));
?>
	</div>
</div>
