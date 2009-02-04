<?php
	$id = next($this->uri);
	if (!empty($this->errors['users'])) include("core/app/elements/Ajax/users/".(($id)?"edit":"new").".php");
	else {
		$new = false;
		if (!$id) { $id = $this->db->Insert_ID(); $new = true; }
		$user = $this->get("users")->find("*", "id='".$id."'")->fields();
		if ($new) { ?><tr id="user_<?php echo $id; ?>"><?php } ?>
		<td><?php echo $user['first_name']; ?></td>
		<td><?php echo $user['last_name']; ?></td>
		<td>*****</td>
		<td><?php echo $user['email']; ?></td>
		<td><?php echo $user['security']; ?></td>
		<td class="options"><a class="button" href="#" onclick="edit_user(<?php echo $user['id']; ?>);return false;">Edit</a>
			<form id="del_form" action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
				<input id="action[users]" name="action[users]" type="hidden" value="delete"/>
				<input type="hidden" name="users[id]" value="<?php echo $user['id']; ?>"/>
				<input class="button" type="submit" onclick="return confirm('Are you sure you want to delete?');" value="Delete"/>
			</form>
		</td>
<?php if ($new) echo "</tr>"; } ?>