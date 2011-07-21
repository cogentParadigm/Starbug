<?php function box_top($ops="") { ?>
		<?php
			$ops = star($ops);
			efault($ops['class'], 'box');
			$ops['echo'] = false;
			echo str_replace("</div>", "", tag(array_merge(array("div"), $ops)));
		?>
		<div class="inside">
<?php } ?>
<?php function box_bottom() { ?>
			<br class="clear"/>
		</div>
	</div>
<?php } ?>
<?php function content_top($content="", $box="") { ?>
	<?php
		global $request; if ($request->format != "xhr") {
			$ops = star($content);
			efault($ops['id'], 'content');
			$ops['echo'] = false;
			echo str_replace("</div>", "", tag(array_merge(array("div"), $ops)));
		}
	?>
	<?php box_top($box); ?>
<?php } ?>
<?php function content_bottom() { ?>
	<br class="clear"/>
	<?php box_bottom(); ?>
<?php global $request; if ($request->format != "xhr") { ?></div><?php } ?>
<?php } ?>
