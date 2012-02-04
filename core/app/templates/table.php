<table <?php html_attributes($attributes); ?>>
<?php if (!empty($columns)) { ?>
	<thead>
		<tr>
			<?php foreach ($columns as $label => $col) { ?><th <?php html_attributes($col); ?>><?php echo $label; ?></th><?php } ?>
		</tr>
	<thead>
<?php } ?>
<?php if (!empty($rows)) { ?>
	<?php foreach ($rows as $row) { ?>
		<tr>
			<?php foreach ($row as $k => $v) { ?><td><?php echo $v; ?></td><?php } ?>
		</tr>
	<?php } ?>
<?php } ?>
</table>
