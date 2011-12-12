	<script type="text/javascript">
		<?php global $dojo; if (!empty($dojo->requires)) { ?>
		require([
					<?php foreach ($dojo->requires as $idx => $mid) { if ($idx > 0) echo ",\n"; ?>"<?php echo $mid; ?>"<?php } echo "\n\t\t"; ?>
		]);
		<?php } ?>
	</script>
