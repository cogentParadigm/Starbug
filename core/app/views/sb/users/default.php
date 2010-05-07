	<?php
		$sb->import("util/pager", "util/templates");
		efault($_GET['orderby'], "created");
		efault($_GET['direction'], "desc");
		echo form("method:get",
			"hidden  orderby", "hidden  direction", "text  keywords  class:left round-left", "submit  class:round-right button  value:Search"
		)."<br/>";
		$pager = new pager($sb->query("users", "action:read  keywords:$_GET[keywords]  search:username,email  orderby:$_GET[orderby] $_GET[direction]"), 25, end($this->uri));
?>
<h2><a id="add_user" class="right round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>Users</h2>
<?php $pager->links("sb/users/"); ?>
<?php if ($sb->record_count == 0) { ?>
<p>Nothing found</p>
<?php } else { ?>
	<table id="users_table" class="clear lister">
	<?php echo table_headers("Username", "Memberships"); ?>
	<?php while($user = $pager->item()) { ?>
		<tr id="user_<?php echo $user['id']; ?>">
			<td>
				<a href="<?php echo uri("sb/users/update/$user[id]"); ?>"><?php echo $user['username']; ?></a>
				<ul class="row-actions">
					<li class="first"><a href="<?php echo uri("sb/users/update/$user[id]"); ?>">edit</a></li>
					<li><?php $_POST['users'] = $user; echo form("model:users  action:delete", "submit  class:link  value:delete"); ?></li>
				</ul>
			</td>
			<td><?php $glist = ""; foreach($this->groups as $g => $n) if ($user['memberships'] & $n) $glist .= $g.", "; echo rtrim($glist, ", "); ?></td>
		</tr>
	<?php } ?>
	</table>
<?php } ?>
<a id="add_user" class="big left round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>
