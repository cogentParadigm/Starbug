<aside id="left-panel">

	-	<!-- User info -->
	<div class="login-info">
		<span>
			<?php if (!empty($this->user->userinfo("photo"))) { ?>
			<img src="<?php echo $this->images->thumb($this->user->userinfo("photo"), ["w" => 64, "h" => 64]); ?>" alt="me" class="online" />
			<?php } ?>
			<span class="username">
				<a href="<?php echo $this->url->build("profile"); ?>"><?php echo $this->user->userinfo("first_name")." ".$this->user->userinfo("last_name"); ?></a>
			</span>
		</span>
	</div>
	<!-- end user info -->

	<!-- NAVIGATION -->
	<nav>
		<?php
			$this->assign("attributes", array());
			$this->assign("menu", "admin");
			$this->render("menu");
		?>
	</nav>

	<span class="minifyme" data-action="minifyMenu">
		<i class="fa fa-arrow-circle-left hit"></i>
	</span>

	<a style="position:absolute;left:0;right:0;bottom:20px;display;block;margin:0 auto;max-width:80%" href="./"><img style="max-width:100%" src="<?php echo $this->url->build("app/themes/storm/public/images/logo.png"); ?>" alt="<?php echo $this->settings->get("site_name"); ?>"/></a>

</aside>
<!-- END NAVIGATION -->
