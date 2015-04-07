<div class="blocks <?php echo $region; ?>-blocks">
	<?php
		$blocks = query("blocks")->condition("blocks.uris_id", $response->id)->condition("blocks.region", $region)->sort("position");
		foreach ($blocks as $block) {
			$this->assign("block", $block);
			$this->render(array($response->path."-".$region."-block", $response->path."-block", $region."-block", "block"));
		}
	?>
</div>
