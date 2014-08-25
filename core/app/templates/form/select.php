<?php if(isset($attributes['multiple'])) $attributes['name'] .= '[]'; ?>
<select <? html_attributes($attributes); ?>>
	<? foreach ($options as $caption => $val) { ?>
		<option value="<?= htmlentities($val); ?>"<? if ((is_array($value) && in_array($val, $value)) || ($val == $value)) echo " selected=\"true\""; ?>><?= $caption; ?></option>
	<? } ?>
</select>
