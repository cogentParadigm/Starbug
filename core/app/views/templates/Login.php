<?php
if (($token = next($this->uri)) == "logout") {
	$_SESSION[P('id')] = 0;
	$_SESSION[P('memberships')] = 0;
}
if (!($_SESSION[P('memberships')] & 1)) $body_class = "login";
include("core/app/views/header.php");
include($this->file);
include("core/app/views/footer.php");
?>
