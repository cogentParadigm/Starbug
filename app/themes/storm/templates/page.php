<input type="checkbox" id="menu-checkbox">
<div id="menu">
	<?php
		assign("menu", "admin");
		render("menu");
	?>
</div>
<div class="page <?= request("layout"); ?>">
	<? render("regions"); ?>
</div>
