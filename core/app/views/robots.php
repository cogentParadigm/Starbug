<?php
	$hide = option("seo_hide");
?>
User-agent: *
Disallow: <?php if ($hide) echo "/"; ?>
