<div id="top-right">
	<a href="<?php echo uri("logout"); ?>" class="small right blue button" style="margin:2px 0 0 5px"><span>Logout</span></a>
	<a href="#"><?php echo userinfo("username"); ?></a>
</div>
<h1><a href="./"><span><?php echo Etc::WEBSITE_NAME; ?></span></a></h1>
<ul id="tabs" class="hnav">
		<li><a class="button" href="<?php echo uri("profile"); ?>"><span>Profile</span></a></li>
</ul>
<ul id="nav" class="hnav dropdown">
	<li class="first">
		<a <?php if (in_array($request->uri[1], array("settings", "menus", "taxonomies"))) { ?>class="active" <?php } ?>href="<?php echo uri("admin/settings"); ?>">Settings</a>
		<ul>
			<li><a href="<?php echo uri("admin/settings"); ?>">General</a></li>
			<li><hr style="width:160px;margin-left:20px"/></li>
			<li><a href="<?php echo uri("admin/menus"); ?>">Menus</a></li>
			<li><a href="<?php echo uri("admin/taxonomies"); ?>">Taxonomy</a></li>
			<?php if (logged_in("root")) { ?>
				<li><hr style="width:160px;margin-left:20px"/></li>
				<li><a href="<?php echo uri("sb-admin"); ?>" target="_blank">The Bridge</a></li>
			<?php } ?>
		</ul>
	</li>
	<li><a <?php if ($request->uri[1] == "users") { ?>class="active" <?php } ?>href="<?php echo uri("admin/users"); ?>">Users</a></li>
	<li><a <?php if ($request->uri[1] == "uris") { ?>class="active" <?php } ?>href="<?php echo uri("admin/uris"); ?>">Pages</a></li>
</ul>
