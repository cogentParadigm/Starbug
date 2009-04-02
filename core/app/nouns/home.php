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
			<h2>Getting Started</h2>
			<p>Here are a few tips to help you get started.</p>
			<ul class="decimal">
				<li>To create a home page, add a new uri via the dashboard below using '<?php echo Etc::DEFAULT_PATH; ?>' as the path, and whatever you'd like as the template. The new page and template will be in <em>app/nouns/</em>.</li>
				<li><p>To create a data model, such as Articles, go to the models page via the dashboard below and do the following:<p>
						<ul class="decimal">
							<li><p>click the 'new model' button, enter 'Article' and hit save.</p></li>
							<li><p>click on the Article model, hit 'new field' and enter 'name'</p></li>
							<li><p>click 'add key' and add descriptors for the Article's 'name'</p></li>
							<li>These are simply key/value pairs, such as type=string, length=5:64 unique=true, default=0, input_type=select, and range=0:10 to name a few</li>
							<li>Repeat steps 2 - 4 for all fields you want to add to your model</li>
							<li>hit 'activate'</li>
						</ul>
				</li>
				<li>Now you'll want some CRUD (create, read, update, delete), which we will generate in the form of SULC (show, update, list, create)
					<div class="codeblock"><p>./script/generate crud article -l name</p></div>
					<span class="note">Note: the -l option signifies the models labelling field. eg. This article model is labelled by it's name field</span>
				</li>
			</ul>
			<?php } ?>
