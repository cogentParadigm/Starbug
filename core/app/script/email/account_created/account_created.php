<?php
$uid = array_shift($argv);
$pass = array_shift($argv);
$u = query("users", "select:*  where:users.id='$uid'  limit:1");
$mailer->Subject = "[".settings("site_name")."] Your account has been created!";
$mailer->Body = "<div><span style=\"font-weight:bold;font-size:24px\">".settings("site_name")."</span><br/><br/>"
			   ."<span style=\"font-weight:bold;color:#419BC0;font-size:16px\">Your account has been created.</span><br /><br/>"
			   ."To login, follow this link:<br/><a style=\"color:#419BC0\" href=\"".uri("")."\">".uri("")."</a><br/><br/>"
			   ."<span style=\"font-weight:bold;\">Username:</span>$u[username]<br />"
			   ."<span style=\"font-weight:bold;\">Password:</span>$pass</div>";
$mailer->AddAddress($u['email'], $u['first_name']." ".$u['last_name']);
?>
