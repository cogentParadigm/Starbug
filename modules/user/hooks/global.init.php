<?php
		if (!isset($_SESSION[P('id')])) {
			$_SESSION[P('id')] = $_SESSION[P('memberships')] = 0;
			$_SESSION[P('user')] = array();
		}
?>
