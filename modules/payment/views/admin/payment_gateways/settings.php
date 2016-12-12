<div class="panel panel-default">
	<div class="panel-heading"><strong><span><?php echo $gateway["name"]." Settings"; ?></span></strong></div>
	<div class="panel-body">
	<?php
		$this->render(array($model."/admin-toolbar", "admin-toolbar"));
		$this->displays->render(ucwords($model)."Grid", array("gateway" => $gateway["id"]));
	?>
	</div>
</div>
