<div class="container">
	<a id="logo" href="./"><span><?php echo Etc::WEBSITE_NAME; ?></span></a>
	<span id="subhead"><?php echo Etc::TAGLINE; ?></span>
	<ul id="nav" class="right hnav">
		<li><a class="active" href="">Home</a></li>
		<?php if (logged_in()) { ?>
			<li><a href="<?php echo uri("logout"); ?>">Log Out</a></li>
		<?php } else { ?>
			<li><a href="<?php echo uri("login"); ?>">Log In</a></li>
		<?php } ?>
	</ul>
</div>
