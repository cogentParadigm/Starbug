<div class="blocks <?php echo $region; ?>-blocks">
	<?php
		$blocks = $this->db->query("blocks")->condition("blocks.uris_id", $response->id)->condition("blocks.region", $region)->sort("position");
		foreach ($blocks as $block) {
			$this->displays->render("BlockDisplay", array("block" => $block));
		}
	?>
</div>
