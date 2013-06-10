<?php
	foreach ($tokens as $name => $token) {
		if ($name == "name") $name = "site_name";
		$replacements[$token] = settings($name);
	}
?>
