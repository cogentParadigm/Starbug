			<h2>Congratulations, she rides!</h2>
			<p>You've successfully started the Starbug engine on your server!</p>
			<?php if (Etc::DB_NAME == "") { ?>
			<h2>Before you begin</h2>
			<p>Take the following steps to get up and running with Starbug.</p>
			<ul class="decimal">
				<li>run the installer script:
					<div class="codeblock"><p>./etc/install.php</p></div>
				</li>
				<li>delete the installer script:
					<div class="codeblock"><p>rm etc/install.php</p></div>
				</li>
				<li><p>Refresh this page.</p></li>
			</ul>
			<?php } else if (empty($_SESSION[P('id')])) { ?>
			<h2>Login</h2>
			<p>Now go ahead and login!</p>
			<form id="login" method="post" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>">
				<input id="action[users]" name="action[users]" type="hidden" value="login"/>
				<div class="field">
					<label for="email">Email</label>
					<?php if (!empty($this->errors['users']['loginMatchError'])) { ?><span class="error">That combination does not match.</span><?php } ?>
					<input id="email" name="users[email]" type="text"<?php if (!empty($_POST['users']['email'])) { ?> value="<?php echo $_POST['users']['email']; ?>"<?php } ?> maxlength="64" />
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input id="password" name="users[password]" type="password" />
				</div>
				<input class="button" type="submit" value="Login" />
			</form>
			<?php } else if (($_SESSION[P("memberships")] & 1)==1) { ?>
			<h2>Why am I seeing this page?</h2>
			<p>This page is temporary and will only be displayed as long as there is no Home page in your application.</p>
			<?php } ?>
