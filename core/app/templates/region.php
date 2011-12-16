<div id="<?= $region; ?>" class="region">
	<? $route = $request->payload['path']; render(array($route."-".$region, $region)); ?>
</div>
