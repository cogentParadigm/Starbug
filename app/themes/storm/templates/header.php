<div id="top-right" class="nav">
	<a href="<?php echo uri("logout"); ?>" class="small right blue button" style="margin:2px 0 0 5px"><span>Logout</span></a>
	<a href="#"><?php echo userinfo("username"); ?></a>
</div>
<h1><a href="./"><span><?php echo settings("site_name"); ?></span></a></h1>
<ul id="tabs" class="nav nav-tabs">
		<li><a class="button" href="<?php echo uri("profile"); ?>"><span>Profile</span></a></li>
</ul>
<?php
	assign("attributes", array("id" => "nav"));
	assign("menu", "admin");
	render("menu");
?>
<!--
<ul id="nav" class="nav">
	<li class="first dropdown" data-dojo-type="bootstrap/Dropdown">
		<a class="dropdown-toggle<?php if (in_array($request->uri[1], array("settings", "menus", "taxonomies"))) { ?> active<?php } ?>" role="button" data-toggle="dropdown">Settings<b class="caret"></b></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="<?php echo uri("admin/settings"); ?>">General</a></li>
			<li class="divider"></li>
			<li><a href="<?php echo uri("admin/menus"); ?>">Menus</a></li>
			<li><a href="<?php echo uri("admin/taxonomies"); ?>">Taxonomy</a></li>
			<?php foreach (locate("settings.menu.php", "hooks") as $path) include($path); ?>
			<?php if (logged_in("root")) { ?>
				<li class="divider"></li>
				<li><a href="<?php echo uri("sb-admin"); ?>" target="_blank">The Bridge</a></li>
			<?php } ?>
		</ul>
	</li>
	<li><a <?php if ($request->uri[1] == "users") { ?>class="active" <?php } ?>href="<?php echo uri("admin/users"); ?>">Users</a></li>
	<li><a <?php if ($request->uri[1] == "uris") { ?>class="active" <?php } ?>href="<?php echo uri("admin/uris"); ?>">Pages</a></li>
</ul>
-->
