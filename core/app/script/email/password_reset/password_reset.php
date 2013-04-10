<?php
$uid = array_shift($argv);
$pass = array_shift($argv);
$u = query("users", "select:*  where:users.id='$uid'  limit:1");
$mailer->Subject = "Your ".settings("site_name")." password has been reset";
$mailer->Body = "<div>Hi $u[first_name] $u[last_name],<br /><div style=\"font-weight:bold;\">"
							 ."Your new password is ".$pass.".<br />Click <a href=\"".uri("login")."\">here</a> to return to the login page.</div></div>";
$mailer->AddAddress($u['email'], $u['first_name']." ".$u['last_name']);
?>
