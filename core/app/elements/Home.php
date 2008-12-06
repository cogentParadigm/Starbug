<h2>Congratulations, she rides!</h2>
			<p>You've successfully started the Starbug engine on your server!</p>
			<h2>Getting Started</h2>
			<p>Take the following steps to get up and running with Starbug.</p>
			<?php if (Etc::DB_NAME == "") { ?>
			<ul id="get_started">
				<li>Create a database for your project.</li>
				<li>Edit <em>etc/Etc.php</em> and enter your database details and any other details.</li>
				<li>Run the core migrations.
					<div class="codeblock"><p>./core/db/migrate</p></div>
					<span class="note"><strong>Note:</strong> before you do this, you might want to edit some of the migrations in <em>core/db/migrations/</em>.</span>
				</li>
				<li>Refresh this page.</li>
			<?php } else { ?>
			<p>Now that you've got the database configured, you're ready to begin.</p>
			<ul id="get_started">
				<li></li>
			<?php } ?>
			</ul>