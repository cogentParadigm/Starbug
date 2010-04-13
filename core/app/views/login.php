<?php if (!empty($_SESSION[P('id')])) { ?>
		<form id="logout_form" action="<?php echo ($submit_to) ? $submit_to : htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
		<input id="action['users']" name="action[users]" type="hidden" value="logout" />
		<input class="button" type="submit" value="Log Out" />
	</form>
<?php } else { ?>
	<form id="login_form" action="<?php echo ($submit_to) ? $submit_to : htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
		<input id="action[users]" name="action[users]" type="hidden" value="login"/>
		<div class="field">
			<label for="username">Username</label>
			<?php if (!empty($sb->errors['users']['username']['loginMatch'])) { ?><span class="error"><?php echo $sb->errors['users']['username']['loginMatch']; ?></span><?php } ?>
			<input id="username" class="text" name="users[username]" type="text"<?php if (!empty($_POST['users']['username'])) { ?> value="<?php echo $_POST['users']['username']; ?>"<?php } ?> maxlength="64" />
		</div>
		<div class="field">
			<label for="password">Password</label>
			<input id="password" class="text" name="users[password]" type="password" />
		</div>
		<input class="round button" type="submit" value="Log In" />
	</form>
<?php } ?>
