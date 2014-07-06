<?php foreach($containers as $container) { ?>
	<label><?php echo ucwords(str_replace("_", " ", $container['region'])); ?></label>
	<?php
		$region = $container['region'];
		$position = $container['position'];
		$type = $container['type'];
		$block_id = "block-".$region."-".$position;
		$_POST[$block_id] = $container;
		$attributes['id'] = $block_id;
		$attributes['name'] = $block_id."[content]";
		assign("attributes", $attributes);
		assign("value", $container['content']);
		render("form/textarea");
	?>
<?php } ?>
