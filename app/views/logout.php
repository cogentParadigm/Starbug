<?php
	$_SESSION = array();
	$_SESSION[P("id")] = $_SESSION[P("memberships")] = 0;
	redirect(uri());
?>
