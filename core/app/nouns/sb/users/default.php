<?php
	$sb->import("util/pager");
	$pager = new pager($sb->query("users", "action:read"), 25, next($this->uri));
?>
<h2>Users</h2>
<a id="add_user" class="right round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>
<?php include("core/app/nouns/sb/settings/nav.php"); ?>
<?php $pager->links("sb/users/"); ?>
<table id="users_table" class="clear lister">
<?php foreach(array("thead", "tfoot") as $t) { ?><?php echo "<$t>"; ?><tr><th class="email-col">Email</th><th class="meberships-col">Memberships</th></tr><?php echo "</$t>"; ?><?php } ?>
<?php while($user = $pager->item()) { ?>
	<tr id="user_<?php echo $user['id']; ?>">
		<td>
			<a href="<?php echo uri("sb/users/update/$user[id]"); ?>"><?php echo $user['email']; ?></a>
			<ul class="row-actions">
				<li class="first"><a href="<?php echo uri("sb/users/update/$user[id]"); ?>">edit</a></li>
				<li><a href="<?php echo uri($this->path."?action=delete&id=$user[id]"); ?>">delete</a></li>
			</ul>
		</td>
		<td><?php echo $user['memberships']; ?></td>
	</tr>
<?php } ?>
</table>
<a id="add_user" class="big left round button" href="<?php echo uri("sb/users/create"); ?>">Create User</a>
