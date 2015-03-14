<?php
	if (logged_in("admin") || logged_in("root")) $this->render("form/field");
?>
