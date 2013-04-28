<div id="top-right" class="nav">
	<a href="<?php echo uri("logout"); ?>" class="small right blue button">Logout</a>
	<a href="#"><?php echo userinfo("username"); ?></a>
</div>
<h1><a href="./"><span><?php echo settings("site_name"); ?></span></a></h1>
<ul id="tabs" class="nav nav-tabs">
		<li><a class="button" href="<?php echo uri("profile"); ?>">Profile</a></li>
</ul>
<?php
	assign("attributes", array("id" => "nav"));
	assign("menu", "admin");
	render("menu");
?>
