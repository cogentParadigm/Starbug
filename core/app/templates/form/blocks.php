<?php foreach($containers as $container) { ?>
	<label><?php echo ucwords(str_replace("_", " ", $container['region'])); ?></label>
	<?php
		$region = $container['region'];
		$position = $container['position'];
		$type = $container['type'];
		$block_id = $region."-".$position;
		$_POST[$block_id] = $container;
		$attributes['id'] = "block-".$block_id;
		$attributes['name'] = $display->get_name("blocks[".$block_id."]");
		assign("attributes", $attributes);
		assign("value", $container['content']);
		render("form/textarea");
	?>
<?php } ?>
