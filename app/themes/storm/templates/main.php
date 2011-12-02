<div id="main" class="<?= $request->layout; ?>">
	<?
		if ($layout == "one-column") render("content");
		else if ($layout == "two-column-left") { ?>
			<? render("sidebar"); ?>
			<? render("content"); ?>	
		<? } else { ?>
			<? render("content"); ?>
			<? render("sidebar"); ?>
		<? } ?>
	<ul id="footer" class="small right clear">
		<li>Powered by <a href="http://www.starbugphp.com">Starbug PHP</a></li>
	</ul>
</div>
<?php $sb->publish("footer"); ?>
