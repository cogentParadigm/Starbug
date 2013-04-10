<div class="container">
	<a id="logo" href="./"><span><?php echo settings("site_name"); ?></span></a>
	<span id="subhead"><?php echo settings("tagline"); ?></span>
	<ul id="nav" class="right hnav">
		<li><a class="active" href="<?php echo uri(); ?>">Home</a></li>
		<?php if (logged_in()) { ?>
			<li><a href="<?php echo uri("logout"); ?>">Log Out</a></li>
		<?php } else { ?>
			<li><a href="<?php echo uri("login"); ?>">Log In</a></li>
		<?php } ?>
	</ul>
</div>
