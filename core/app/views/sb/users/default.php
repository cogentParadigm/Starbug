<h2><a id="add_user" class="right round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>Users</h2>
	<?php
		$sb->import("util/form", "util/lister");
		efault($_GET['orderby'], "created");
		efault($_GET['direction'], "desc");
		echo form("method:get",
			"hidden  orderby", "hidden  direction", "text  keywords  class:left round-left", "submit  class:round-right button  value:Search"
		)."<br/>";
		$lister = new lister(
			"orderby:$_GET[orderby] $_GET[direction]  renderer:user_row  show:25  page:".end($this->uri),
			uri("sb/users/[page]?keywords=$_GET[keywords]&orderby=[orderby]&direction=[direction]")
		);
		$lister->add_column("username  sortable:");
		$lister->add_column("memberships  sortable:");
		$lister->query("users", "action:read  keywords:$_GET[keywords]  search:username,email");
		$lister->render("id:users_table  class:clear lister");
	?>
<a id="add_user" class="big left round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>
