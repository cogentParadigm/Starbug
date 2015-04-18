<div <?php html_attributes($display->attributes); ?>>
		<?php $name = $display->options["name"]; foreach ($display->items as $item) { if (empty($item['depth'])) $item['depth'] = 0; ?>
			<div class="form-group checkbox" style="padding-left:<?php echo $item['depth']*15; ?>px">
				<label><input <?php html_attributes("type:checkbox  class:left checkbox  name:".$name."[]  value:$item[id]".((in_array($item['id'], $display->options['value'])) ? "  checked:checked" : "")); ?>/><?php echo $item['label']; ?></label>
			</div>
		<?php } ?>
		<input <?php html_attributes("type:hidden  name:".$name."[]  value:-~"); ?>/>
</div>
