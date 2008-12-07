			<h2>Congratulations, she rides!</h2>
			<p>You've successfully started the Starbug engine on your server!</p>
			<?php if (Etc::DB_NAME == "") { ?>
			<h2>Before you begin</h2>
			<p>Take the following steps to get up and running with Starbug.</p>
			<ul class="decimal">
				<li>Create a database for your project.</li>
				<li>Edit <em>etc/Etc.php</em> and enter your database details and any other details.</li>
				<li>Run the core migrations.
					<div class="codeblock"><p>./core/db/migrate</p></div>
					<span class="note"><strong>Note:</strong> before you do this, you might want to edit some of the migrations in <em>core/db/migrations/</em>.</span>
				</li>
				<li>Refresh this page.</li>
			</ul>
			<?php } else { ?>
			<?php if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) { ?>
			<h2>Login</h2>
			<p>Now that you've got the database configured, go ahead and log in as the Administrator</p>
			<form id="login" method="post" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>">
				<input id="action[Users]" name="action[Users]" type="hidden" value="login"/>
				<div class="field">
					<label for="email">Email</label>
					<input id="email" name="user[email]" type="text"<?php if (!empty($_POST['user']['email'])) { ?> value="<?php echo $_POST['user']['email']; ?>"<?php } ?> maxlength="64" />
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input id="password" name="user[password]" type="password" />
				</div>
				<input class="button" type="submit" value="Login" />
			</form>
			<?php } else { ?>
			<h2>Getting Started</h2>
			<p>Here are a few tips to help you get started.</p>
			<ul class="decimal">
				<li>To create a home page, add a new element via the dashboard below using '<?php echo Etc::DEFAULT_PAGE; ?>' as the name, and whatever you'd like as the template. The new template will be in <em>app/elements</em> and the new page will be in <em>app/elements/'templateName'</em>.</li>
				<li><p>To create a data model, such as Articles, start by generating a migration.<p>
						<div class="codeblock"><p>./script/generate migration Articles</p></div>
						<p>Edit the Migration file in <em>/db/migrations</em>, and then migrate the database.</p>
						<div class="codeblock"><p>./db/migrate</p></div>
						<p>You now have a model in <em>/app/models</em>.</p>
						<p>To generate a model without using a migration, use the generater.</p>
						<div class="codeblock"><p>./script/generate model Articles</p></div>
						<span class="note"><strong>Note:</strong> for more information on working with models, refer the documentation.</span>
				</li>
			</ul>
			<?php }
			} ?>