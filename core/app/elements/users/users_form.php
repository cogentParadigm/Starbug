<form<?php if (!empty($formid)) echo " id=\"$formid\""; ?> class="users_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
	<input class="action" name="action[users]" type="hidden" value="<?php echo $action; ?>" />
	<?php if (!empty($_POST['users']['id'])) { ?><input id="id" name="users[id]" type="hidden" value="<?php echo $_POST['users']['id']; ?>" /><?php } ?>
	<div class="field">
		<label for="first_name">First name</label>
		<?php if (!empty($this->errors['users']['first_nameError'])) { ?><span class="error">Please enter a first name.</span><?php } ?>
		<input id="first_name" name="users[first_name]" type="text"<?php if (!empty($_POST['users']['first_name'])) { ?> value="<?php echo $_POST['users']['first_name']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="last_name">Last name</label>
		<?php if (!empty($this->errors['users']['last_nameError'])) { ?><span class="error">Please enter a last name.</span><?php } ?>
		<input id="last_name" name="users[last_name]" type="text"<?php if (!empty($_POST['users']['last_name'])) { ?> value="<?php echo $_POST['users']['last_name']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="password">Password</label>
		<?php if (!empty($this->errors['users']['passwordError'])) { ?><span class="error">Please enter a password.</span><?php } ?>
		<input id="password" name="users[password]" type="password" />
	</div>
	<div class="field">
		<label for="email">Email</label>
		<?php if (!empty($this->errors['users']['emailError'])) { ?><span class="error">Please enter an email address.</span><?php } ?>
		<input id="email" name="users[email]" type="text"<?php if (!empty($_POST['users']['email'])) { ?> value="<?php echo $_POST['users']['email']; ?>"<?php } ?> />
	</div>
	<div class="field">
		<label for="security">Privilages</label>
		<?php dfault($_POST['users']['security'], "2"); ?>
		<select id="security" name="users[security]">
			<option value="2"<?php if ($_POST['users']['security'] == "2") { ?> selected="true"<?php } ?>>2 - Member</option>
			<option value="3"<?php if ($_POST['users']['security'] == "3") { ?> selected="true"<?php } ?>>3 - Moderator</option>
			<option value="4"<?php if ($_POST['users']['security'] == "4") { ?> selected="true"<?php } ?>>4 - Administrator</option>
		</select>
	</div>
	<div><input class="button" type="submit" value="Go" /></div>
</form>