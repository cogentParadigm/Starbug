<div class="blocks <?= $region; ?>-blocks">
	<?
		$blocks = query("uris,blocks", "select:blocks.*  where:uris.id=? && blocks.region=?  orderby:position ASC", array($request->payload['id'], $region));
		foreach ($blocks as $block) {
			assign("block", $block);
			render(array($request->payload['path']."-".$region."-block", $request->payload['path']."-block", $region."-block", "block"));
		}
	?>
</div>
