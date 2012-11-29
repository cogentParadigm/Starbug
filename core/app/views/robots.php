<?php
	$hide = settings("seo_hide");
?>
User-agent: *
Disallow: <?php if ($hide) echo "/"; ?>
