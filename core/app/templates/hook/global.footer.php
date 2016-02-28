	<script type="text/javascript">
<?php if (!empty($response->scripts)) { ?>
		require([
			"<?php echo implode('"'.",\n\t\t\t".'"', $response->getScripts()->get()); ?>"
		]);
<?php } ?>
	</script>
