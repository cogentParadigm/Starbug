<div class="col-sm-6">
	<div class="panel panel-default">
  	<div class="panel-heading">
    	<h3 class="panel-title">Already have an account?</h3>
  	</div>
  	<div class="panel-body" style="min-height:300px">
    	<?php $this->displays->render("LoginForm"); ?>
  	</div>
	</div>
</div>
<div class="col-sm-6">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">New to <?php echo $this->settings->get("site_name"); ?></h3>
		</div>
		<div class="panel-body" style="min-height:300px">
			<br/>
			<a href="<?php echo $this->url->build("signup?to=checkout"); ?>" class="btn btn-default center-block">Sign up now</a>
			<br/>
			<p style="text-align:center;margin:0"><strong>OR</strong></p>
			<br/>
			<a href="<?php echo $this->url->build("checkout/guest"); ?>" class="btn btn-default center-block">Checkout as a guest</a>
			<br/>
		</div>
	</div>
</div>
