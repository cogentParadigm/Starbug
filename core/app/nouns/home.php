<?php
if (file_exists("etc/configure.php")) include("etc/configure.php");
if ($_POST['configure_starbug']) unlink("etc/configure.php");
?>
			<h2>Congratulations, she rides!</h2>
			<p>You've successfully started the Starbug engine on your server!</p>
			<?php if (Etc::DB_NAME == "") { ?>
			<h2>Before you begin</h2>
			<p>Answer the following questions to get up and running with Starbug.</p>
			<form id="install_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="hidden" name="configure_starbug" value="1" />
				<h3>Database details</h3>
				<div class="field">
					<label for="dbtype">Database Type (eg. mysql)</label>
					<input id="dbtype" name="dbtype" type="text" />
				</div>
				<div class="field">
					<label for="dbhost">Database Host (eg. localhost)</label>
					<input id="dbhost" name="dbhost" type="text" />
				</div>
				<div class="field">
					<label for="dbname">Database Name</label>
					<input id="dbname" name="dbname" type="text" />
				</div>
				<div class="field">
					<label for="dbuser">Database User</label>
					<input id="dbuser" name="dbuser" type="text" />
				</div>
				<div class="field">
					<label for="dbpass">Database Password</label>
					<input id="dbpass" name="dbpass" type="password" />
				</div>
				<h3>Site Info</h3>
				<div class="field">
					<label for="prefix">Site Prefix</label>
					<input id="prefix" name="prefix" type="text" />
				</div>
				<div class="field">
					<label for="sitename">Website Name</label>
					<input id="sitename" name="sitename" type="text" />
				</div>
				<div class="field">
					<label for="siteurl">Website URL</label>
					<input id="siteurl" name="siteurl" type="text" />
				</div>
			<h3>Super Admin User</h3>
			<p>Enter the following information about the Super Admin User.</p>
					<div class="field">
						<label for="adminfirst_name">first name</label>
						<input id="adminfirst_name" type="text" name="adminfirst_name" />
					</div>
					<div class="field">
						<label for="adminlast_name">last name</label>
						<input id="adminlast_name" type="text" name="adminlast_name" />
					</div>
					<div class="field">
						<label for="adminemail">email</label>
						<input id="adminemail" type="text" name="adminemail" />
					</div>
					<div class="field">
						<label for="adminpass">password</label>
						<input id="adminpass" type="password" name="adminpass" />
					</div>
					<div><input type="submit" class="button" value="submit" /></div>
				</form>
			<?php } else if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) { ?>
			<h2>Login</h2>
			<p>Now go ahead and login!</p>
			<form id="login" method="post" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>">
				<input id="action[users]" name="action[users]" type="hidden" value="login"/>
				<div class="field">
					<label for="email">Email</label>
					<input id="email" name="users[email]" type="text"<?php if (!empty($_POST['users']['email'])) { ?> value="<?php echo $_POST['users']['email']; ?>"<?php } ?> maxlength="64" />
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input id="password" name="users[password]" type="password" />
				</div>
				<input class="button" type="submit" value="Login" />
			</form>
			<?php } else { ?>
			<h2>Getting Started</h2>
			<p>Here are a few tips to help you get started.</p>
			<ul class="decimal">
				<li>To create a home page, add a new uri via the dashboard below using '<?php echo Etc::DEFAULT_PATH; ?>' as the path, and whatever you'd like as the template. The new page and template will be in <em>app/nouns/</em>.</li>
				<li><p>To create a data model, such as Articles, go to the models page via the dashboard below and do the following:<p>
						<ul class="decimal">
							<li><p>click the 'new model' button, enter 'Articles' and hit save.</p></li>
							<li><p>
						<div class="codeblock"><p>./script/generate migration articles</p></div>
						<p>Edit the Migration file in <em>/db/migrations/</em>, and then migrate the database.</p>
						<div class="codeblock"><p>./db/migrate</p></div>
						<p>You now have a model in <em>/app/models/</em>.</p>
						<p>To generate a model without using a migration, use the generater.</p>
						<div class="codeblock"><p>./script/generate model articles</p></div>
						<span class="note"><strong>Note:</strong> for more information on working with models, refer to the documentation (does not exist yet).</span>
				</li>
			</ul>
			<?php } ?>