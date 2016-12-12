<?php foreach($containers as $container) { ?>
	<label><?php echo ucwords(str_replace("_", " ", $container['region'])); ?></label>
	<?php
		$region = $container['region'];
		$position = $container['position'];
		$type = $container['type'];
		$block_id = $region."-".$position;
		$this->request->setPost($block_id, $container);
		$attributes['id'] = "block-".$block_id;
		$attributes['name'] = $display->get_name("blocks[".$block_id."]");
		$this->assign("attributes", $attributes);
		$this->assign("value", $container['content']);
		$this->render("form/textarea");
	?>
<?php } ?>
