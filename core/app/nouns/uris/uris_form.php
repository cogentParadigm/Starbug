<form<?php if (!empty($formid)) echo " id=\"$formid\""; ?> class="uris_form" action="<?php echo (empty($submit_to) ? htmlentities($_SERVER['REQUEST_URI']) : $submit_to); ?>" method="post">
	<input class="action" name="action[uris]" type="hidden" value="<?php echo $action; ?>" />
<?php if (!empty($_POST['uris']['id'])) { ?><input id="id" name="uris[id]" type="hidden" value="<?php echo $_POST['uris']['id']; ?>" /><?php } ?>
	<div class="field">
		<label for="name">Path</label>
		<?php if (!empty($this->errors['uris']['pathError'])) { ?><span class="error">Please enter a path name.</span><?php } ?>
		<input id="name" name="uris[path]" class="text" type="text"<?php if (!empty($_POST['uris']['path'])) { ?> value="<?php echo $_POST['uris']['path']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="template">Template</label>
		<?php if (!empty($this->errors['uris']['templateError'])) { ?><span class="error">Please enter a template.</span><?php } ?>
		<input id="template" name="uris[template]" class="text" type="text"<?php if (!empty($_POST['uris']['template'])) { ?> value="<?php echo $_POST['uris']['template']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="visibility">Visibility</label>
		<?php dfault($_POST['uris']['visible'], "1"); ?>
		<select id="visibility" name="uris[visible]">
			<option value="0"<?php if ($_POST['uris']['visible'] == "0") { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['uris']['visible'] == "1") { ?> selected="true"<?php } ?>>1</option>
		</select>
	</div>
	<div class="field">
		<label for="importance">Importance</label>
		<select id="importance" name="uris[importance]">
			<option value="0"<?php if ($_POST['uris']['importance'] == "0") { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['uris']['importance'] == "1") { ?> selected="true"<?php } ?>>1</option>
			<option value="2"<?php if ($_POST['uris']['importance'] == "2") { ?> selected="true"<?php } ?>>2</option>
			<option value="3"<?php if ($_POST['uris']['importance'] == "3") { ?> selected="true"<?php } ?>>3</option>
			<option value="4"<?php if ($_POST['uris']['importance'] == "4") { ?> selected="true"<?php } ?>>4</option>
			<option value="5"<?php if ($_POST['uris']['importance'] == "5") { ?> selected="true"<?php } ?>>5</option>
			<option value="6"<?php if ($_POST['uris']['importance'] == "6") { ?> selected="true"<?php } ?>>6</option>
			<option value="7"<?php if ($_POST['uris']['importance'] == "7") { ?> selected="true"<?php } ?>>7</option>
			<option value="8"<?php if ($_POST['uris']['importance'] == "8") { ?> selected="true"<?php } ?>>8</option>
			<option value="9"<?php if ($_POST['uris']['importance'] == "9") { ?> selected="true"<?php } ?>>9</option>
			<option value="10"<?php if ($_POST['uris']['importance'] == "10") { ?> selected="true"<?php } ?>>10</option>
		</select>
	</div>
	<div class="field">
		<label for="collective">Collective</label>
		<?php dfault($_POST['uris']['collective'], "0"); ?>
		<select id="collective" name="uris[collective]">
			<option value="0"<?php if ($_POST['uris']['collective'] == "0") { ?> selected="true"<?php } ?>>everybody</option>
		<?php foreach ($this->groups as $name => $val) { ?>
			<option value="<?php echo $val; ?>"<?php if ($_POST['uris']['collective'] == $val) { ?> selected="true"<?php } ?>><?php echo $name; ?></option>
		<?php } ?>
		</select>
	</div>
	<div><input class="button" type="submit" value="Save" /></div>
</form>
