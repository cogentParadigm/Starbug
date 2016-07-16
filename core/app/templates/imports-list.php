<?php
	$label = $this->models->get($model)->label;
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong><span>Import <?php echo $label; ?></span></strong></div>
	<div class="panel-body">
	<?php
		$this->render("imports/admin-toolbar");
		$this->displays->render("ImportsGrid", array("model" => $model));
	?>
	</div>
</div>
