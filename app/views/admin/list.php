<?php
	efault($query, "admin");
	efault($template, "list");
	$this->assign("query", $query);
	$this->render($template);
?>
