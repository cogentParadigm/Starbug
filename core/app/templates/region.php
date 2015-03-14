<div id="<?php echo $region; ?>" class="region">
	<?php $route = request()->payload['path']; $this->render(array($route."-".$region, $region)); ?>
</div>
