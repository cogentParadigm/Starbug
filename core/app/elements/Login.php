<?php if (!empty($_POST['action']['Users']) && empty($this->errors)) { ?>
	<p>Welcome <?php echo $_POST['user']['first_name']; ?></p>
<?php } else if (empty($_SESSION[P('id')])) { ?>
<form id="login_form" action="<?php htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
	<input id="action[Users]" name="action[Users]" type="hidden" value="login"/>
	<div class="field">
		<label for="email">Email</label>
		<input id="email" name="user[email]" type="text"<?php if (!empty($_POST['user']['email'])) { ?> value="<?php echo $_POST['user']['email']; ?>"<?php } ?> maxlength="64" />
	</div>
	<div class="field">
		<label for="password">Password</label>
		<input id="password" name="user[password]" type="password" />
	</div>
	<input class="button" type="submit" value="Log In" />
</form>
<?php } else { ?>
<form id="logout_form" action="<?php htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
	<input id="action['Users']" name="action[Users]" type="hidden" value="logout" />
	<input class="button" type="submit" value="Log Out" />
</form>
<?php } ?>