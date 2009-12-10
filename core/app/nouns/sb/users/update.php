<?php $id = next($this->uri); $_POST['users'] = $sb->query("users", "action:read	where:id='$id'	limit:1"); ?>
<h2>Update User</h2>
<?php $action = "create"; $submit_to = uri("sb/users"); include("core/app/nouns/sb/users/users_form.php"); ?>
