<?php if ($request->format == "xhr") { ?><div class="box"><div class="inside"><?php } ?>
<?php
	if (!empty($template)) render($template);
	else render("create");
?>
<?php if ($request->format == "xhr") { ?></div></div><?php } ?>
