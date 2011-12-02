<? $layout = request("layout"); efault($layout, "two-column-right"); ?>
<div class="container <?= $layout; ?>">
	<?
		if ($layout == "one-column") render("content");
		else if ($layout == "two-column-left") { ?>
			<? render("sidebar"); ?>
			<? render("content"); ?>	
		<? } else if ($layout == "two-column-right") { ?>
			<? render("content"); ?>
			<? render("sidebar"); ?>
		<? } ?>
</div>
