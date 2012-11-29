<?php
	if (!empty($form)) render_form($form);
	else if (!empty($template)) render($template);
	else render("update");
?>
