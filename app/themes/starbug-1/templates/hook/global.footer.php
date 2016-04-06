	<script type="text/javascript">
<?php $scripts = implode('"'.",\n\t\t\t".'"', $response->getScripts()->get()); ?>
<?php if (!empty($scripts)) { ?>
		require([
			"<?php echo $scripts; ?>"
		]);
<?php } ?>
	</script>
