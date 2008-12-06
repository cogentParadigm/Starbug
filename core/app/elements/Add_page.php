<?php $_POST['page']['visible'] = dfault($_POST['page']['visible'], 0); $_POST['page']['importance'] = dfault($_POST['page']['importance'], 0); $_POST['page']['security'] = dfault($_POST['page']['security'], 2); ?>
<h2>Create Page</h2>
<p>Add a new page.</a>
<form id="action_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
	<input id="action[Actions]" name="action[Actions]" type="hidden" value="create"/>
	<div class="field">
		<label for="name">Name</label>
		<input id="name" name="page[name]" type="text"<?php if (!empty($_POST['page']['name'])) { ?> value="<?php echo $_POST['page']['name']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="template">Template</label>
		<input id="template" name="page[template]" type="text"<?php if (!empty($_post['page']['template'])) { ?> value="<?php echo $_POST['page']['template']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="visibility">Visibility</label>
		<select id="visibility" name="page[visible]">
			<option value="0"<?php if ($_POST['page']['visible']==0) { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['page']['visible']==1) { ?> selected="true"<?php } ?>>1</option>
		</select>
	</div>
	<div class="field">
		<label for="importance">importance</label>
		<select id="importance" name="page[importance]">
			<option value="0"<?php if ($_POST['page']['importance']==0) { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['page']['importance']==1) { ?> selected="true"<?php } ?>>1</option>
			<option value="2"<?php if ($_POST['page']['importance']==2) { ?> selected="true"<?php } ?>>2</option>
			<option value="3"<?php if ($_POST['page']['importance']==3) { ?> selected="true"<?php } ?>>3</option>
			<option value="4"<?php if ($_POST['page']['importance']==4) { ?> selected="true"<?php } ?>>4</option>
			<option value="5"<?php if ($_POST['page']['importance']==5) { ?> selected="true"<?php } ?>>5</option>
			<option value="6"<?php if ($_POST['page']['importance']==6) { ?> selected="true"<?php } ?>>6</option>
			<option value="7"<?php if ($_POST['page']['importance']==7) { ?> selected="true"<?php } ?>>7</option>
			<option value="8"<?php if ($_POST['page']['importance']==8) { ?> selected="true"<?php } ?>>8</option>
			<option value="9"<?php if ($_POST['page']['importance']==9) { ?> selected="true"<?php } ?>>9</option>
			<option value="10"<?php if ($_POST['page']['importance']==10) { ?> selected="true"<?php } ?>>10</option>
		</select>
	</div>
	<div class="field">
		<label for="security">Security</label>
		<select id="security" name="page[security]">
			<option value="0"<?php if ($_POST['page']['security']==0) { ?> selected="true"<?php } ?>>0</option>
			<option value="1"<?php if ($_POST['page']['security']==1) { ?> selected="true"<?php } ?>>1</option>
			<option value="2"<?php if ($_POST['page']['security']==2) { ?> selected="true"<?php } ?>>2</option>
			<option value="3"<?php if ($_POST['page']['security']==3) { ?> selected="true"<?php } ?>>3</option>
			<option value="4"<?php if ($_POST['page']['security']==4) { ?> selected="true"<?php } ?>>4</option>
		</select>
	</div>
	<input class="button" type="submit" value="Create Page" />
</form>