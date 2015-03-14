	<?php open_form("model:users  action:login  url:".$url, "id:login_form"); ?>
		<div class="field"><?php text("username"); ?></div>
		<div class="field"><?php password("password  class:text"); ?></div>
		<div class="field"><?php button("Log In", "type:submit  class:btn-default"); ?><div class="note"><a href="<?php echo uri("forgot-password"); ?>">Forgot Your Password?</a></div></div>
	<?php close_form(); ?>
