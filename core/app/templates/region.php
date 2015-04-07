<div id="<?php echo $region; ?>" class="region">
	<?php $route = $response->path; $this->render(array($route."-".$region, $region)); ?>
</div>
