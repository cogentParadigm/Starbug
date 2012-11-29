<?php
	efault($query, "admin");
	efault($template, "list");
	assign("query", $query);
	render($template);
?>
