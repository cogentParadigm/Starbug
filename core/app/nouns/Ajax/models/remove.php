<?php
	include("core/app/models/Models.php");
	$models = new Models($this->db);
	$loc = next($this->uri);
	$models->remove($loc);
?>
