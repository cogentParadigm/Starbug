<?php
$table = next($this->uri);
if ($table == "uris") {
	$uris = $this->get("uris")->find("id, path")->GetRows();
	foreach ($uris as $uri) { ?>
		<option value="<?php echo $uri['id']; ?>"><?php echo $uri['path']; ?></option>
	<?php }
} else if ($table == "users") {
	$users = $this->get("users")->find("id, first_name, last_name")->GetRows();
	foreach ($users as $user) { ?>
		<option value="<?php echo $user['id']; ?>"><?php echo $user['first_name']." ".$user['last_name']; ?></option>
	<?php }
} ?>
