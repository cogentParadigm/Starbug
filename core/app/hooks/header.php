		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="generator" content="StarbugPHP alpha" />
		<?php if (Etc::BLUEPRINT_ENABLED) { ?>
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/screen.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/print.css"); ?>" type="text/css" media="print">
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php if (Etc::DOJO_ENABLED) { ?>
			<link rel="stylesheet" type="text/css" href="<?php echo uri("app/public/js/dojo/release/dojo/dijit/themes/tundra/tundra.css"); ?>"/>
			<script type="text/javascript" src="<?php echo uri("app/public/js/dojo/release/dojo/dojo/dojo.js"); ?>" djConfig="parseOnLoad: true"></script>
		<?php } ?>
