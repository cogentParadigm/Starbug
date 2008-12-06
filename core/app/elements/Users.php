<?php
$users = $this->get_object("Users");
if (!isset($_GET["page"]) || !is_numeric($_GET["page"])) $_GET["page"] = 0;
$allusers = $users->afind("*");
$totalusers = $users->recordCount;
$userlist = $users->afind("*", "", "ORDER BY id DESC LIMIT ".($_GET['page']*25).", 25");
$currentusers = $users->recordCount;
?>
<h2>Users</h2>
<?php if ($totalusers > 25) { ?>
<ul class="users">
	<?php if ($_GET['page'] > 0) { ?>
	<li class="back"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;page=<?php echo $_GET["page"]-1; ?>">Back</a></li>
	<?php } for($i=0;$i<ceil($totalusers/25);$i++) { ?>
	<li><a<?php if($_GET["page"] == $i) { ?> class="active"<?php } ?> href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;page=<?php echo $i; ?>"><?php echo $i+1; ?></a></li>
	<?php } if($_GET["page"] < ceil($totalusers/25)-1) { ?>
	<li class="next"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;page=<?php echo $_GET["page"]+1; ?>">Next</a></li>
	<?php } ?>
</ul>
<?php } ?>
<table id="users_table">
<tr><th>Name</th><th>Email</th><th>Password</th><th>Security</th><th>Options</th></tr>
<?php foreach($userlist as $user) { ?>
	<tr id="user<?php echo $user['id']; ?>">
		<td><?php echo $user['username']; ?></td>
		<td><?php echo $user['email']; ?></td>
		<td>*****</td>
		<td><?php echo $page['security']; ?></td>
		<td><a class="button" href="#" onclick="javascript:$.get('<?php echo htmlentities('?action=Edit_user&id='.$user['id']); ?>', function(data) {$('#user<?php echo $user['id']; ?>').html(data);});return false;">Edit</a><form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
			<input id="action[Users]" name="action[Users]" type="hidden" value="delete"/><input type="hidden" name="user[id]" value="<?php echo $user['id']; ?>"/><input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/></form></td>
	</tr>
<?php } ?>
</table>
<a id="adduser" class="button" href="#" onclick="javascript:$.post('<?php echo htmlentities('?action=Edit_user'); ?>', {'action[Users]': 'create', 'user[username]': 'New User'}, function(data) {$('#users_table').append(data);});return false;">Add User</a>