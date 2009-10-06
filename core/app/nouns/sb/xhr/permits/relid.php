<?php
$table = next($this->uri);
if ($table == "uris") {
	$uris = $sb->query("uris", "action:read");
	foreach ($uris as $uri) { ?>
		<option value="<?php echo $uri['id']; ?>"><?php echo $uri['path']; ?></option>
	<?php }
} else if ($table == "users") {
	$users = $sb->query("users", "action:read");
	foreach ($users as $user) { ?>
		<option value="<?php echo $user['id']; ?>"><?php echo $user['first_name']." ".$user['last_name']; ?></option>
	<?php }
} ?>
