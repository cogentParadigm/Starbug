<?php open_form("model:$model  action:$action  url:$url"); ?>
	<?php
		efault($cancel_url, $url);
	?>
	<?php foreach ($fields as $name => $field) { ?>
		<?php
			if (is_string($field['display'])) {
				$values = explode(",", $field['display']);
				if (in_array($action, $values)) $field['display'] = true; 
			}	
		?>
		<?php if ($field['display'] === true) { ?>
				<?php
					if ($field['input_type'] == "password") $field['class'] .= ((empty($field['class'])) ? "" : " ")."text";
					else if ($field['input_type'] == "select") {
						if (!empty($field['filters']['alias'])) {
							$ref = explode(" ", $field['filters']['references']);
							$field['caption'] = $field['filters']['alias'];
							$field['value'] = end($ref);
							$field['from'] = reset($ref);
						}
					}
					$star = "";
					foreach ($field as $k => $v) $star .= "  $k:$v";
				?>
				<?php $field['input_type']($name.$star); ?>
				<?php
					if (!empty($field['filters']['confirm'])) {
						$field['input_type']($field['filters']['confirm'].$star);
					}
				?>
		<?php } ?>
	<?php } ?>
	<div class="field"><button class="left positive" type="submit">Save</button><button class="negative cancel button"<?php if (!empty($cancel_url)) { ?> onclick="window.location='<?= $cancel_url; ?>'"<?php } ?>>Cancel</button></div>
<?php close_form(); ?>
