<?php function box_top() { ?>
	<div class="box-top"><div></div></div>
	<div class="box">
		<div class="inside">
<?php } ?>
<?php function box_bottom() { ?>
			<div class="left"></div>
		</div>
	</div>
<?php } ?>
<?php function content_top() { ?>
<?php global $request; if ($request->format != "xhr") { ?><div id="content"><?php } ?>
	<?php box_top(); ?>
<?php } ?>
<?php function content_bottom() { ?>
	<br class="clear"/>
	<?php box_bottom(); ?>
<?php global $request; if ($request->format != "xhr") { ?></div><?php } ?>
<?php } ?>