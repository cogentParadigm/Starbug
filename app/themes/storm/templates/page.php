<input type="checkbox" id="menu-checkbox">
<div id="menu">
	<?php
		$this->assign("menu", "admin");
		$this->render("menu");
	?>
</div>
<div class="page <?php echo $response->layout; ?>">
	<?php $this->render("regions"); ?>
</div>
