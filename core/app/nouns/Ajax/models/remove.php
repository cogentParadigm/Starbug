<?php
	include("core/app/models/Models.php");
	$models = new Models("core/db/schema");
	$loc = next($this->uri);
	$models->remove($loc);
?>
