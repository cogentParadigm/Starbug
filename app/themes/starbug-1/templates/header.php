<div class="container">
	<a id="logo" href="./"><img src="<?php echo uri("app/themes/starbug-1/public/images/logo.png"); ?>" title="<?php echo $this->settings->get("site_name"); ?>"/></a>
	<ul id="nav" class="pull-right hnav">
		<li><a class="active" href="<?php echo uri(); ?>">Home</a></li>
		<?php if ($this->user->loggedIn()) { ?>
			<li><a href="<?php echo uri("logout"); ?>">Log Out</a></li>
		<?php } else { ?>
			<li><a href="<?php echo uri("login"); ?>">Log In</a></li>
		<?php } ?>
	</ul>
</div>
