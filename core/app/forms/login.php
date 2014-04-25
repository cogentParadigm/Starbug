	<? open_form("model:users  action:login  url:".$url, "id:login_form"); ?>
		<div class="field"><? text("username"); ?></div>
		<div class="field"><? password("password  class:text"); ?></div>
		<div class="field"><? button("Log In", "type:submit  class:btn-default"); ?><div class="note"><a href="<?= uri("forgot-password"); ?>">Forgot Your Password?</a></div></div>
	<? close_form(); ?>
