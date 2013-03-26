	<script type="text/javascript">
		<?php global $js; if (!empty($js->requires)) { ?>
		require([
					<?php foreach ($js->requires as $idx => $mid) { if ($idx > 0) echo ",\n"; ?>"<?php echo $mid; ?>"<?php } echo "\n\t\t"; ?>
		]);
		<?php } ?>
	</script>
