<?php
$users = $this->get("users");
$page = next($this->uri);
empty_nan($page, 0);
$all = $users->afind("*");
$total = $users->recordCount;
$list = $users->afind("*", "", "ORDER BY id DESC LIMIT ".($page*25).", 25");
$shown = $users->recordCount;
?>
<script type="text/javascript">
	function new_user() {
		dojo.xhrGet({
			url: '<?php echo uri("users/new"); ?>',
			load: function (data) {
				dojo.byId('users_table').innerHTML += data;
			}
		});
	}
	function save_new() {
		dojo.xhrPost({
			url: '<?php echo uri("users/get"); ?>',
			form: 'new_user_form',
			load: function(data) {
				cancel_new();
				dojo.byId('users_table').innerHTML += data;
			}
		});
	}
	function cancel_new() {
		var newrow = dojo.byId('new_user');
		newrow.parentNode.removeChild(newrow);
	}
	function edit_user(id) {
		dojo.xhrGet({
			url: '<?php echo uri("users/edit/"); ?>'+id,
			load: function(data) {
				dojo.byId('user_'+id).innerHTML = data;
			}
		});
	}
	function save_edit(id) {
		dojo.xhrPost({
			url: '<?php echo uri("users/get/"); ?>'+id,
			form: 'edit_user_form',
			load: function(data) {
				dojo.byId('user_'+id).innerHTML = data;
			}
		});
	}
	function cancel_edit(id) {
		dojo.xhrGet({
			url: '<?php echo uri("users/get/"); ?>'+id,
			load: function(data) {
				dojo.byId('user_'+id).innerHTML = data;
			}
		});
	}
</script>
<h2>Users</h2>
<?php include("core/app/nouns/settings/nav.php"); ?>
<?php if ($total > 25) { ?>
<ul class="pages">
	<?php if ($page > 0) { ?>
	<li class="back"><a href="users/list/<?php echo $page-1; ?>">Back</a></li>
	<?php } for($i=0;$i<ceil($total/25);$i++) { ?>
	<li><a<?php if($page == $i) { ?> class="active"<?php } ?> href="users/list/<?php echo $i; ?>"><?php echo $i+1; ?></a></li>
	<?php } if($page < ceil($total/25)-1) { ?>
	<li class="next"><a href="users/list/<?php echo $page+1; ?>">Next</a></li>
	<?php } ?>
</ul>
<?php } ?>
<table id="users_table">
<tr><th>First Name</th><th>Last Name</th><th>Password</th><th>Email</th><th>Memberships</th><th>Options</th></tr>
<?php foreach($list as $user) { ?>
	<tr id="user_<?php echo $user['id']; ?>">
		<td><?php echo $user['first_name']; ?></td>
		<td><?php echo $user['last_name']; ?></td>
		<td>*****</td>
		<td><?php echo $user['email']; ?></td>
		<td><?php echo $user['memberships']; ?></td>
		<td class="options"><a class="button" href="#" onclick="edit_user(<?php echo $user['id']; ?>);return false;">Edit</a>
			<form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[Users]" name="action[Users]" type="hidden" value="delete"/>
				<input type="hidden" name="users[id]" value="<?php echo $user['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/>
			</form>
		</td>
	</tr>
<?php } ?>
</table>
<a id="add_user" class="button" href="users/create" onclick="new_user();return false;">New User</a>
