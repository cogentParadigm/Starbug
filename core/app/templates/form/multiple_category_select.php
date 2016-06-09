<div class="multiple_category_select">
	<?php if (empty($value)) $value = array(); foreach ($terms as $term) { ?>
		<div class="form-group checkbox" style="padding-left:<?php echo $term['depth']*15; ?>px">
			<?php
				$attrs = ['type' => 'checkbox', 'class' => 'left checkbox', 'name' => $name.'[]', 'value' => $term['id']];
				if (in_array($term['id'], $value)) $attrs['checked'] = 'checked';
			?>
			<label><input <?php echo $this->filter->attributes($attrs); ?>/><?php echo $term['term']; ?></label>
		</div>
	<?php } ?>
	<input <?php echo $this->filter->attributes(["type" => "hidden", "name" => $name."[]", "value" => "-~"]); ?>/>
</div>
