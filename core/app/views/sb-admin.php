<?php
if (!$this->user->loggedIn()) {
	$submit_to = uri("sb-admin");
	include("core/app/views/login.php");
} else include("core/app/views/rogue/default.php");
?>
