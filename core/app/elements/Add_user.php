<?php if (!empty($_POST['action']['Users']) && empty($this->errors)) { ?>
	<p id="factSubmitted">Thank You! A new user has been created.</p>
<?php } else { ?>
<form id="user_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
	<input id="action[Users]" name="action[Users]" type="hidden" value="create"/>
	<fieldset>
		<?php if (!empty($this->errors)) { ?>
		<p><span class="error">Oops! There were some mistakes in your form. Please double check the information you entered.</span></p>
		<?php } ?>
		<div class="field">
			<label for="firstname">First Name</label>	
			<input id="firstname" name="user[first_name]" type="text"<?php if (!empty($_POST['user']['first_name'])) { ?> value="<?php echo $_POST['user']['first_name']; ?>"<?php } ?> maxlength="32" />
		</div>
		<div class="field">
			<label for="lastname">Last Name</label>
			<input id="lastname" name="user[last_name]" type="text"<?php if (!empty($_POST['user']['last_name'])) { ?> value="<?php echo $_POST['user']['last_name']; ?>"<?php } ?> />
		</div>
		<div class="field">
			<label for="email">Email Address</label>
			<input id="email" name="user[email]" type="text"<?php if (!empty($_POST['email'])) { ?> value="<?php echo $_POST['email']; ?>"<?php } ?> />
		</div>
		<div class="field">
			<label for="emailConfirm">Confirm Email Address</label>
			<input id="emailConfirm" name="user[email_confirm]" type="text"<?php if (!empty($_POST['user']['email_confirm'])) { ?> value="<?php echo $_POST['user']['email_confirm']; ?>"<?php } ?> />
		</div>
		<div class="field">
			<label for="password">Password</label>
			<input id="password" name="user[password]" type="password" />
		</div>
		<div class="field">
			<label for="password_confirm">Confirm Password</label>
			<input id="password_confirm" name="user[password_confirm]" type="password" />
		</div>
		<div class="field">
			<label for="security">Privilages</label>
			<select id="security" name="user[security]">
				<option value="2"<?php if ($_POST['user']['security'] == 2) { ?> selected="true"<?php } ?>>2 - Member</option>
				<option value="3"<?php if ($_POST['user']['security'] == 3) { ?> selected="true"<?php } ?>>3 - Moderator</option>
				<option value="4"<?php if ($_POST['user']['security'] == 4) { ?> selected="true"<?php } ?>>4 - Administrator</option>
			</select>
		</div>
		<input class="button" type="submit" value="Add User" />
	</fieldset>
</form>
<?php } ?>