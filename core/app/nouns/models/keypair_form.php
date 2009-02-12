<form<?php if (!empty($formid)) echo " id=\"$formid\""; ?> class="keypair_form" method="post">
	<input name="add_key" type="hidden" value="<?php echo $loc; ?>" />
	<div class="field">
		<label for="name">Key</label>
		<?php if (!empty($this->errors['models']['keyError'])) { ?><span class="error">Please enter a key.</span><?php } ?>
		<input id="name" name="key" type="text"<?php if (!empty($_POST['key'])) { ?> value="<?php echo $_POST['key']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="template">Value</label>
		<?php if (!empty($this->errors['models']['valueError'])) { ?><span class="error">Please enter a value.</span><?php } ?>
		<input id="template" name="value" type="text"<?php if (!empty($_POST['value'])) { ?> value="<?php echo $_POST['value']; ?>"<?php } ?> />
	</div>
</form>