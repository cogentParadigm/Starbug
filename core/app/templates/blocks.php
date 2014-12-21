<div class="blocks <?= $region; ?>-blocks">
	<?
		$blocks = query("blocks")->condition("blocks.uris_id", request()->payload['id'])->condition("blocks.region", $region)->sort("position");
		foreach ($blocks as $block) {
			assign("block", $block);
			render(array($request->payload['path']."-".$region."-block", $request->payload['path']."-block", $region."-block", "block"));
		}
	?>
</div>
