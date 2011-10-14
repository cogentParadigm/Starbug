		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="generator" content="StarbugPHP" />
	<?php if (Etc::ENVIRONMENT == "production") { ?>
		<?php if (Etc::BLUEPRINT_ENABLED) { ?>
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-screen.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-print.css"); ?>" type="text/css" media="print">
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php if (Etc::DOJO_ENABLED) { ?>
			<script type="text/javascript" src="<?php echo uri("app/public/js/dojo/release/dojo/dojo/dojo.js"); ?>" data-dojo-config="parseOnLoad: true"></script>
		<?php } ?>
			<!--[if lt IE 9]>
				<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
	<?php } else { ?>
		<?php if (Etc::BLUEPRINT_ENABLED) { ?>
			<?php
				$bp = theme("styles", $request->theme);
				efault($bp['plugins'], array());
				efault($bp['screen'], array());
				efault($bp['print'], array());
				efault($bp['ie'], array());
			?>
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/reset.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/typography.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/forms.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/grid.css"); ?>" type="text/css" media="screen, projection">
			<?php if (file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-screen.css")) { ?>
				<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/custom-screen.css"); ?>" type="text/css" media="screen, projection">
			<?php } ?>
			<?php if (file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-print.css")) { ?>
				<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/custom-print.css"); ?>" type="text/css" media="print">
			<?php } ?>
			<?php if (file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-ie.css")) { ?>
				<!--[if IE]><link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/custom-ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
			<?php } ?>
			<?php foreach ($bp['screen'] as $screen) { ?>
				<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/$screen"); ?>" type="text/css" media="screen, projection">
			<?php } ?>
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/print.css"); ?>" type="text/css" media="print">
			<?php foreach ($bp['print'] as $print) { ?>
				<link rel="stylesheet" href="<?php echo uri($print); ?>" type="text/css" media="print">
			<?php } ?>
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
			<?php foreach ($bp['ie'] as $ie) { ?>
				<!--[if IE]><link rel="stylesheet" href="<?php echo uri($ie); ?>" type="text/css" media="screen, projection"><![endif]-->
			<?php } ?>
			<?php foreach ($bp['plugins'] as $plugin) { ?>
					<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/screen.css"); ?>" type="text/css" media="screen, projection">
					<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/print.css"); ?>" type="text/css" media="print">
					<!--[if IE]><link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
			<?php } ?>
		<?php } ?>
		<?php if (Etc::DOJO_ENABLED) { ?>
			<?php
				$profile = file_get_contents(BASE_DIR."/app/public/js/dojo.profile.js");
				$profile = explode("\n", substr($profile, 15));
				foreach ($profile as $i => $p) if (0 === strpos(trim($p), "//")) unset($profile[$i]);
				$profile = str_replace(array("dependencies:", "layers:", "name:", "prefixes:", "'"), array('"dependencies":', '"layers":', '"name":', '"prefixes":', '"'), implode(" ", $profile));
				$profile = json_decode($profile, true);
				$paths = "";
				foreach ($profile['prefixes'] as $p) $paths .= "'$p[0]':'$p[1]', ";
			?>
			<script type="text/javascript" src="<?php echo uri("app/public/js/dojo/dojo/dojo.js"); ?>" data-dojo-config="parseOnLoad: true, modulePaths:{<?php echo rtrim($paths, ', '); ?>}, serverTime:'<?php echo date("Y-m-d H:i:s"); ?>'"></script>
			<script type="text/javascript">
			<?php foreach ($profile['layers'] as $l) { foreach ($l['dependencies'] as $d) { ?>
				dojo.require("<?php echo $d; ?>");
			<?php } } ?>
			</script>
		<?php } ?>
	<?php } ?>
	<script type="text/javascript">
		var WEBSITE_URL = '<?php echo uri(); ?>';
	</script>
