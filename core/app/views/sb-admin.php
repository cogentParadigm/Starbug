<?php
if (!logged_in()) {
	$submit_to = uri("sb-admin");
	include("core/app/views/login.php");
} else include("core/app/views/rogue/default.php");
?>
