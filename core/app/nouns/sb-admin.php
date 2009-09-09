<?php
if (($token = next($this->uri)) == "logout") {
	$_SESSION[P('id')] = 0;
	$_SESSION[P('memberships')] = 0;
}
if (($_SESSION[P('memberships')] & 1) != 1) {
	$submit_to = uri("sb-admin");
	include("core/app/nouns/login.php");
} else { ?>
<h2>Admin</h2>
<h3>tools</h3>
<?php include("core/app/nouns/include/toolnav.php"); ?>
<h3>settings</h3>
<?php include("core/app/nouns/settings/nav.php"); ?>
<?php } ?>
