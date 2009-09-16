<?php
$what = next($this->uri);
if ($what == "user") {
	$users = $sb->get("users")->get("id, first_name, last_name")->GetRows();
	foreach ($users as $user) { ?>
		<option value="<?php echo $user['id']; ?>"><?php echo $user['first_name']." ".$user['last_name']; ?></option>
	<?php	}
} else if ($what == "group") {
	foreach ($this->groups as $name => $number) { ?>
		<option value="<?php echo $number; ?>"><?php echo $name; ?></option>
	<?php }
} ?>
