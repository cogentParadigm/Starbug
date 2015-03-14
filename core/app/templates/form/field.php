<?php
	if ($display) $form = $display;
	if (!empty($prepend)) {
		echo $prepend;
	}
?>
<?php $field_name = rtrim(end(explode("[", $name)), ']'); ?>
<?php if (!$nodiv) { ?><div class="form-group <?php echo $model."-".$field_name; ?> <?php
	if (!empty($div)) {
		echo $div." ";
	}
	echo ($control == "input") ? $type : $control;
	$object_id = $form->get('id');
	if ($required) {
		echo " required";
		$this->assign("required", true);
	} else $this->assign("required", false);
?>"><?php } ?>
	<?php
		if ($control != "input" || ($type != "checkbox" && $type != "radio")) $this->render("form/label");
		if (!empty($between)) {
			echo $between;
		}
		if (!empty($form->errors[$field])) {
			$attributes['class'] .= " form-error";
			$this->assign("attributes", $attributes);
		}
		if (!empty($form->errors[$field])) {
			foreach ($form->errors[$field] as $error => $message) {
				$this->assign("error", $message);
				$this->render("form/error");
			}
		}
		if ($control == "input" && ($type == "checkbox" || $type == "radio")) $this->render("form/label");
	?>
<? if ($control != "input" || ($type != "checkbox" && $type != "radio")) $this->render(array("$model/form/$field-$control", "form/$field-$control", "$model/form/$control", "form/$control")); ?>
<?php
	if (!empty($after)) {
		echo $after;
	}
?>
<?php if (!empty($info)) { ?>
	<span class="help-block"><?php echo nl2br($info); ?></span>
<?php } ?>
<?php if (!$nodiv) { ?></div><?php } ?>
<?php
	if (!empty($append)) {
		echo $append;
	}
?>
