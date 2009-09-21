<?php
	$from_copy = array("app/template.php" => "app/nouns/$template.php",
										 "app/home.php" => "app/nouns/".Etc::DEFAULT_PATH.".php",
										 "app/header.php" => "app/nouns/header.php",
										 "app/footer.php" => "app/nouns/footer.php",
										 "app/missing.php" => "app/nouns/missing.php"
										);
	$paths = array("path, template, collective" => "'".Etc::DEFAULT_PATH."', '$template', '0'");
?>
