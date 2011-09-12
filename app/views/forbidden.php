<div id="content">
	<?php if (logged_in()) { ?>
		<h2>Sorry!</h2>
		<p>You do not have sufficient permission to access this page.</p>
	<?php } ?>
	<h2>Log In</h2>
	<p>Please log in to continue.</p>
	<?php render("form/login"); ?>
</div>
