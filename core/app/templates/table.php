<table <?php echo $this->filter->attributes($attributes); ?>>
<?php if (!empty($columns)) { ?>
	<thead>
		<tr>
			<?php foreach ($columns as $label => $col) { ?><th <?php echo $this->filter->attributes($col); ?>><?php echo $label; ?></th><?php } ?>
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
