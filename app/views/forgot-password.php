<div id="content">
<?php if($this->db->success("users", "reset_password")) { ?>
	<p>Your new password has been emailed to you.<br />Click <a href="<?php echo $this->url->build('login');?>">here</a> to return to the login page.</p>
<?php } else {
?>
	<h2>Forgot Password</h2>
	<P>Unable to login or forgot your password?<br />Enter your email address below and we'll reset<br />your password and email it to you.</p>
	<?php $this->displays->render("PasswordResetForm"); ?>
<?php } ?>
</div>
