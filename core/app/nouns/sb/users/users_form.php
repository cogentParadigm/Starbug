	<?php
		$sb->import("util/form");
		$fields = array();
		$fname_errors = array("first_name" => "Please enter a first name.");
		$fields["first_name"] = array("type" => "text", "errors" => $fname_errors);
		$lname_errors = array("last_name" => "Please enter a last name.");
		$fields["last_name"] = array("type" => "text", "errors" => $lname_errors);
		$pass_errors = array("password" => "Please enter a password.");
		$fields["password"] = array("type" => "password", "errors" => $pass_errors);
		$email_errors = array("email" => "Please enter an email.");
		$fields["email"] = array("type" => "text", "errors" => $email_errors);
		$mem_errors = array("memberships" => "Please enter a memberships value.");
		$fields["memberships"] = array("type" => "text", "errors" => $mem_errors);
		$fields["Save"] = array("type" => "submit", "class" => "big left button");
		echo form::render("users", "post", $action, $submit_to, $fields);
	?>
