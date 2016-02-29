<div class="container">
	<a id="logo" href="./"><img src="<?php echo $this->url->build("app/themes/starbug-1/public/images/logo.png"); ?>" title="<?php echo $this->settings->get("site_name"); ?>"/></a>
	<ul id="nav" class="pull-right hnav">
		<li><a class="active" href="<?php echo $this->url->build(); ?>">Home</a></li>
		<?php if ($this->user->loggedIn()) { ?>
			<li><a href="<?php echo $this->url->build("logout"); ?>">Log Out</a></li>
		<?php } else { ?>
			<li><a href="<?php echo $this->url->build("login"); ?>">Log In</a></li>
		<?php } ?>
	</ul>
</div>
