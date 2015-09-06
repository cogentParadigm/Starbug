<?php
	if (empty($query)) $query = "admin";
	if (empty($template)) $template = "list";
	$this->assign("query", $query);
	$this->render($template);
?>
