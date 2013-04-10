<?php
	if (logged_in("admin") || logged_in("root")) render("form/field");
?>
