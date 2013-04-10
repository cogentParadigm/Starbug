<?php $field_name = rtrim(end(explode("[", $name)), ']'); ?>
<?php if (!$nodiv) { ?><div class="input <?php
	if (!empty($div)) {
		echo $div;
		assign("div", "");
	}
	echo ($control == "input") ? $type : $control;
	$object_id = $form->get('id');
	if (!empty($form->schema) && !empty($form->schema[$field_name]) && !isset($form->schema[$field_name]['filters']['default']) && !isset($form->schema[$field_name]['null']) && (!isset($form->schema[$field_name]['filters']['optional_update']) || empty($object_id))) {
		echo " required";
		assign("required", true);
	} else assign("required", false);
?>"><?php } ?>
	<?php if ($type != "checkbox" && $type != "radio") render("form/label"); ?>
	<?php
		if (!empty($between)) {
			echo $between; assign("between", "");
		}
		if (!empty($errors[$field])) {
			$attributes['class'] .= " form-error";
			assign("attributes", $attributes);
		}
	?>
	<?php
		if (!empty($errors[$field])) {
			foreach ($errors[$field] as $error => $message) {
				assign("error", $message);
				render("form/error");
			}
		}
	?>
<? render(array("$model/form/$field-$control", "form/$field-$control", "$model/form/$control", "form/$control")); ?>
<?php if ($type == "checkbox" || $type == "radio") render("form/label"); ?>
<?php
	if (!empty($after)) {
		echo $after; assign("after", "");
	}
?>
<?php if (!empty($info)) { ?>
	<span class="info"><?php echo $info; assign("info", ""); ?></span>
<?php } ?>
<?php if (!$nodiv) { ?></div><?php } else assign("nodiv", false); ?>
