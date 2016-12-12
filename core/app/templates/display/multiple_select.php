<div <?php echo $this->filter->attributes($display->attributes); ?>>
		<?php $name = $display->options["name"]; $found = array(); foreach ($display->items as $item) { if (empty($item['depth'])) $item['depth'] = 0; ?>
			<div class="form-group checkbox" style="padding-left:<?php echo $item['depth']*15; ?>px">
				<?php
					$attrs = ['type' => 'checkbox', 'class' => 'left checkbox', 'name' => $name."[]", 'value' => $item['id']];
					if (in_array($item['id'], $display->options['value'])) {
						$found[] = $item['id'];
						$attrs['checked'] = 'checked';
					}
				?>
				<label><input <?php echo $this->filter->attributes($attrs); ?>/><?php echo $item['label']; ?></label>
			</div>
		<?php } ?>
		<?php if ($display->options['other_option']) { ?>
			<?php
				$other_id = $this->filter->normalize($name)."_other";
				$other_value = "";
				foreach ($display->options['value'] as $v) {
					if (!in_array($v, $found)) {
						//this value is not from the predefined list
						$other_value = $v;
					}
				}
				$other_attrs = ["id" => $other_id, "type" => "checkbox", "class" => "left checkbox", "name" => $name."[]", "value" => $other_value];
				if (!empty($other_value)) $other_attrs['checked'] = 'checked';
			?>
			<div class="form-group checkbox">
				<div style="padding-left:20px"><input <?php echo $this->filter->attributes($other_attrs); ?>/><span><?php echo $display->options['other_option']; ?></span> <input type="text" style="width:150px;display:inline-block" value="<?php echo $other_value; ?>" oninput="var cb = document.getElementById('<?php echo $other_id; ?>');cb.value = this.value;cb.checked=true;" class="form-control"/></div>
			</div>
		<?php } ?>
</div>
