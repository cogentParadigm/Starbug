<div id="content">
<?php if(success("users", "reset_password")) { ?>
	<p>Your new password has been emailed to you.<br />Click <a href="<?php echo uri('login');?>">here</a> to return to the login page.</p>
<?php } else {
	open_form("model:users  action:reset_password");
?>
	<h2>Forgot Password</h2>
	<P>Unable to login or forgot your password?<br />Enter your email address below and we'll reset<br />your password and email it to you.</p>
	<div class="field"><?php text("email"); ?></div>
	<div class="field"><?php button("Reset Password"); ?></div>
</form>
<?php } ?>
</div>
