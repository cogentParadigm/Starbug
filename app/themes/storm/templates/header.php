<div id="top-right">
	<a href="<?php echo uri("logout"); ?>" class="small right blue button" style="margin:2px 0 0 5px"><span>Logout</span></a>
	<a href="#"><?php echo userinfo("username"); ?></a>
</div>
<h1><a href="./"><span><?php echo Etc::WEBSITE_NAME; ?></span></a></h1>
<ul id="tabs" class="hnav">
		<li><a class="button" href="<?php echo uri("profile"); ?>"><span>Profile</span></a></li>
</ul>
<ul id="nav" class="hnav">
	<li class="first"><a href="<?php echo uri("admin/settings"); ?>">Settings</a></li>
	<li><a href="<?php echo uri("admin/uris"); ?>">Pages</a></li>
	<li><a href="<?php echo uri("admin/menus"); ?>">Menus</a></li>
	<li><a href="<?php echo uri("admin/taxonomies"); ?>">Taxonomy</a></li>
</ul>
