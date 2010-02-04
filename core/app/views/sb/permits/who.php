<?php
$what = next($this->uri);
if ($what == "user") {
	$users = $sb->query("users");
	foreach ($users as $user) { ?>
		<option value="<?php echo $user['id']; ?>"><?php echo $user['email']; ?></option>
	<?php	}
} else if ($what == "group") {
	foreach ($this->groups as $name => $number) { ?>
		<option value="<?php echo $number; ?>"><?php echo $name; ?></option>
	<?php }
} ?>
