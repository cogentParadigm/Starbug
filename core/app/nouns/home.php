			<?php if (Etc::DB_NAME == "") { ?>
			<h2>Before you begin</h2>
			<p>Take the following steps to get up and running with Starbug.</p>
			<ul class="decimal">
				<li>run the installer script:
					<div class="codeblock"><p><strong>$</strong> chmod +x etc/install.php</p><p><strong>$</strong> ./etc/install.php</p></div>
				</li>
				<li>delete the installer script:
					<div class="codeblock"><p><strong>$</strong> rm etc/install.php</p></div>
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
					<?php if (!empty($sb->errors['users']['loginMatchError'])) { ?><span class="error">That combination does not match.</span><?php } ?>
					<input id="email" name="users[email]" type="text"<?php if (!empty($_POST['users']['email'])) { ?> value="<?php echo $_POST['users']['email']; ?>"<?php } ?> maxlength="64" />
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input id="password" name="users[password]" type="password" />
				</div>
				<input class="button" type="submit" value="Login" />
			</form>
			<?php } else if (($_SESSION[P("memberships")] & 1)==1) { ?>
			<h2>Congratulations, she rides!</h2>
			<p>You've successfully started the Starbug engine on your server!</p>
			<p>You can log in again by visiting <a href="<?php echo uri("sb-admin"); ?>">the admin panel</a></p>
			<h2>Getting Started</h2>
			<p>Get started by generating a basic app. Visit <a href="<?php echo uri("generate"); ?>">the generator</a> or run the following command:</p>
			<div class="codeblock"><p><strong>$</strong> ./script/generate app</p></div>
			<?php } ?>
