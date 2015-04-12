	<script type="text/javascript">
		<?php if (!empty($response->scripts)) { ?>
		require([
					<?php foreach ($response->scripts as $idx => $mid) { if ($idx > 0) echo ",\n"; ?>"<?php echo $mid; ?>"<?php } echo "\n\t\t"; ?>
		]);
		<?php } ?>
	</script>
