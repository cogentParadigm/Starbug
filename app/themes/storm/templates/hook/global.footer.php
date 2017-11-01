<?php
	$response->js("storm/main");
	$response->js("bootstrap/Dropdown");
?>
	<script type="text/javascript">
<?php $scripts = implode('"'.",\n\t\t\t".'"', $response->getScripts()->get()); ?>
<?php if (!empty($scripts)) { ?>
		require([
			"<?php echo $scripts; ?>"
		]);
<?php } ?>
	</script>
