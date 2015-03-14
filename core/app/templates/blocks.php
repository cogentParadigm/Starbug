<div class="blocks <?php echo $region; ?>-blocks">
	<?php
		$blocks = query("blocks")->condition("blocks.uris_id", $request->payload['id'])->condition("blocks.region", $region)->sort("position");
		foreach ($blocks as $block) {
			$this->assign("block", $block);
			$this->render(array($request->payload['path']."-".$region."-block", $request->payload['path']."-block", $region."-block", "block"));
		}
	?>
</div>
