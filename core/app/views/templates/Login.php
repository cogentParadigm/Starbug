<?php
if (($token = next($this->uri)) == "logout") {
	$_SESSION[P('id')] = 0;
	$_SESSION[P('memberships')] = 0;
}
if (!logged_in()) {
	$body_class = "login";
	include("core/app/views/header.php");
	include($this->file);
	include("core/app/views/footer.php");
} else if (logged_in("root")) {
	include($this->file);
} else {
	header("Location: ".uri());
}
?>
