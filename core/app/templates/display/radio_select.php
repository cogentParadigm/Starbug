<div <?php html_attributes($display->attributes); ?>>
		<?php $name = $display->options["name"]; $found = false; foreach ($display->items as $item) { if (empty($item['depth'])) $item['depth'] = 0; ?>
			<div class="form-group radio" style="padding-left:<?php echo $item['depth']*15; ?>px">
				<?php
					$attrs = "type:radio  name:".$name."  value:".$item['id'];
					if ($item['id'] == $display->options['value']) {
						$found = true;
						$attrs .= "  checked:checked";
					}
				?>
				<label><input <?php html_attributes($attrs); ?>/><?php echo $item['label']; ?></label>
			</div>
		<?php } ?>
		<?php if ($display->options['other_option']) { ?>
			<?php
				$other_id = normalize($name)."_other";
				$other_value = "";
				if (!$found) {
					$other_value = $display->options['value'];
				}
			?>
			<div class="form-group radio">
				<div style="padding-left:20px"><input <?php html_attributes("id:$other_id  type:radio  name:".$name."  style:margin-top:10.5px  value:$other_value".((!empty($other_value)) ? "  checked:checked" : "")); ?>/><span><?php echo $display->options['other_option']; ?></span> <input type="text" style="width:150px;display:inline-block" value="<?php echo $other_value; ?>" oninput="var rb = document.getElementById('<?php echo $other_id; ?>');rb.value = this.value;rb.checked=true;" class="form-control"/></div>
			</div>
		<?php } ?>
</div>
