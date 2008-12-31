<form<?php if (!empty($formid)) echo " id=\"$formid\""; ?> class="elements_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
	<input class="action" name="action[elements]" type="hidden" value="<?php echo $action; ?>" />
<?php if (!empty($_POST['elements']['id'])) { ?><input id="id" name="elements[id]" type="hidden" value="<?php echo $_POST['elements']['id']; ?>" /><?php } ?>
	<div class="field">
		<label for="name">Path</label>
		<?php if (!empty($this->errors['elements']['pathError'])) { ?><span class="error">Please enter a path name.</span><?php } ?>
		<input id="name" name="elements[path]" type="text"<?php if (!empty($_POST['elements']['path'])) { ?> value="<?php echo $_POST['elements']['path']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="template">Template</label>
		<?php if (!empty($this->errors['elements']['templateError'])) { ?><span class="error">Please enter a template.</span><?php } ?>
		<input id="template" name="elements[template]" type="text"<?php if (!empty($_POST['elements']['template'])) { ?> value="<?php echo $_POST['elements']['template']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="visibility">Visibility</label>
		<?php dfault($_POST['elements']['visible'], "1"); ?>
		<select id="visibility" name="elements[visible]">
			<option value="0"<?php if ($_POST['elements']['visible'] == "0") { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['elements']['visible'] == "1") { ?> selected="true"<?php } ?>>1</option>
		</select>
	</div>
	<div class="field">
		<label for="importance">Importance</label>
		<select id="importance" name="elements[importance]">
			<option value="0"<?php if ($_POST['elements']['importance'] == "0") { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['elements']['importance'] == "1") { ?> selected="true"<?php } ?>>1</option>
			<option value="2"<?php if ($_POST['elements']['importance'] == "2") { ?> selected="true"<?php } ?>>2</option>
			<option value="3"<?php if ($_POST['elements']['importance'] == "3") { ?> selected="true"<?php } ?>>3</option>
			<option value="4"<?php if ($_POST['elements']['importance'] == "4") { ?> selected="true"<?php } ?>>4</option>
			<option value="5"<?php if ($_POST['elements']['importance'] == "5") { ?> selected="true"<?php } ?>>5</option>
			<option value="6"<?php if ($_POST['elements']['importance'] == "6") { ?> selected="true"<?php } ?>>6</option>
			<option value="7"<?php if ($_POST['elements']['importance'] == "7") { ?> selected="true"<?php } ?>>7</option>
			<option value="8"<?php if ($_POST['elements']['importance'] == "8") { ?> selected="true"<?php } ?>>8</option>
			<option value="9"<?php if ($_POST['elements']['importance'] == "9") { ?> selected="true"<?php } ?>>9</option>
			<option value="10"<?php if ($_POST['elements']['importance'] == "10") { ?> selected="true"<?php } ?>>10</option>
		</select>
	</div>
	<div class="field">
		<label for="security">Security</label>
		<?php dfault($_POST['elements']['security'], "2"); ?>
		<select id="security" name="elements[security]">
			<option value="0"<?php if ($_POST['elements']['security'] == "0") { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['elements']['security'] == "1") { ?> selected="true"<?php } ?>>1</option>
			<option value="2"<?php if ($_POST['elements']['security'] == "2") { ?> selected="true"<?php } ?>>2</option>
			<option value="3"<?php if ($_POST['elements']['security'] == "3") { ?> selected="true"<?php } ?>>3</option>
			<option value="4"<?php if ($_POST['elements']['security'] == "4") { ?> selected="true"<?php } ?>>4</option>
		</select>
	</div>
	<div><input class="button" type="submit" value="Go" /></div>
</form>