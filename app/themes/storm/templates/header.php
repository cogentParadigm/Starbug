<div id="top-right" class="nav">
	<a href="<?php echo uri("logout"); ?>" class="small right blue button">Logout</a>
	<a href="#"><?php echo userinfo("username"); ?></a>
</div>
<a id="logo" href="./"><span><?php echo $this->settings->get("site_name"); ?></span></a>
<ul id="tabs" class="nav nav-tabs">
		<li><a class="button" href="<?php echo uri("profile"); ?>">Profile</a></li>
</ul>
<label for="menu-checkbox" id="menu-toggle"></label>
<?php
	$this->assign("attributes", array("id" => "nav"));
	$this->assign("menu", "admin");
	$this->render("menu");
?>
