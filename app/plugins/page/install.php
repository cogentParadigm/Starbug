<?php
	$dirs = array("app/nouns/templates", "app/nouns/page");
	$from_copy = array(
		"page/schema/page" => "core/db/schema/page",
		"page/schema/page.info" => "core/db/schema/.info/page",
		"page/models/Page.php" => "app/models/Page.php",
		"page/nouns/page/default.php" => "app/nouns/page/default.php",
		"page/nouns/page/form.php" => "app/nouns/page/form.php",
		"page/nouns/page/create.php" => "app/nouns/page/create.php",
		"page/nouns/page/update.php" => "app/nouns/page/update.php"
	);
	$paths = array("path, template, visible, collective" => "'page', '$template', '1', '$collective'");
?>
