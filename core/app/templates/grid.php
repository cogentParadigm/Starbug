<table <?php html_attributes($options); ?>>
	<thead>
		<tr>
			<?php foreach ($columns as $col) { ?><th <?php html_attributes($col); ?>><?php echo $col['caption']; ?></th><?php } ?>
		</tr>
	<thead>
</table>
