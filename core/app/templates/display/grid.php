<?php
	//move row options to end
	$row_options = $display->fields['row_options'];
	unset($display->fields['row_options']);
	$display->fields['row_options'] = $row_options;
?>
<table <?php echo $this->filter->attributes($display->attributes); ?>>
<thead>
	<tr>
		<?php foreach ($display->fields as $field => $options) { ?><th <?php echo $this->filter->attributes($display->column_attributes($field, $options)); ?>><?php echo $options['label']; ?></th><?php } ?>
	</tr>
<thead>
</table>
