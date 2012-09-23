		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<title><?php echo strip_tags($request->payload['title']).' - '.Etc::WEBSITE_NAME; ?></title>
		<meta name="description" content="<?php echo $request->payload['description']; ?>"/>
		<?php echo $request->payload['meta']; ?>
		<base href="<?php echo uri(); ?>"/>
		<?php if (!empty($request->payload['canonical'])) { ?><link rel="canonical" href="<?php echo $request->payload['canonical']; ?>"/><?php } ?>
		<?php echo option("meta"); ?>
	<?php if (Etc::ENVIRONMENT == "production") { ?>
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-screen.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-print.css"); ?>" type="text/css" media="print">
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
			<!--[if lt IE 9]>
				<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
		<?php if (Etc::DOJO_ENABLED) { ?>
			<script type="text/javascript" src="<?php echo uri("core/app/public/js/dojo/release/dojo/dojo/dojo.js"); ?>" data-dojo-config="parseOnLoad: true"></script>
		<?php } ?>
	<?php } else { ?>
			<?php
				$styles = theme("styles", $request->theme);
				efault($styles['plugins'], array());
				efault($styles['screen'], array());
				efault($styles['print'], array());
				efault($styles['ie'], array());
				efault($styles['blueprint'], false);
			?>
		<?php if ($styles['blueprint']) { ?>
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/reset.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/typography.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/forms.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/grid.css"); ?>" type="text/css" media="screen, projection">
		<?php } ?>
		<?php if (file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-screen.css")) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/custom-screen.css"); ?>" type="text/css" media="screen, projection">
		<?php } ?>
		<?php if (file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-print.css")) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/custom-print.css"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php if (file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-ie.css")) { ?>
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/custom-ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php foreach ($styles['screen'] as $screen) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/$screen"); ?>" type="text/css" media="screen, projection">
		<?php } ?>
		<?php if ($styles['blueprint']) { ?>
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/print.css"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php foreach ($styles['print'] as $print) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/styesheets/$print"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php if ($styles['blueprint']) { ?>
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php foreach ($styles['ie'] as $ie) { ?>
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/$ie"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php foreach ($styles['plugins'] as $plugin) { ?>
				<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/screen.css"); ?>" type="text/css" media="screen, projection">
				<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/print.css"); ?>" type="text/css" media="print">
				<!--[if IE]><link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php if (Etc::DOJO_ENABLED) { ?>
			<?php
				$profile = file_get_contents(BASE_DIR."/etc/dojo.profile.js");
				$profile = explode("\n", substr($profile, 15));
				foreach ($profile as $i => $p) if (0 === strpos(trim($p), "//")) unset($profile[$i]);
				$profile = str_replace(array("dependencies:", "layers:", "name:", "prefixes:", "'"), array('"dependencies":', '"layers":', '"name":', '"prefixes":', '"'), implode(" ", $profile));
				$profile = json_decode($profile, true);
				$paths = "";
				foreach ($profile['prefixes'] as $p) $paths .= "'$p[0]':'$p[1]', ";
			?>
			<script type="text/javascript" src="<?php echo uri("core/app/public/js/dojo/dojo/dojo.js"); ?>" data-dojo-config="async: true, parseOnLoad: true, serverTime:'<?php echo date("Y-m-d H:i:s"); ?>'"></script>
			<script type="text/javascript">
				require({
						packages: [<?php foreach ($profile['prefixes'] as $idx => $p) { if ($idx > 0) echo ','; echo "\n"; ?>
								{ name: '<?php echo $p[0]; ?>', location: '<?php echo $p[1]; ?>' }<?php } echo "\n"; ?>
						]
				}, [
					<?php foreach ($profile['layers'] as $lidx => $l) { foreach ($l['dependencies'] as $didx => $d) { if ($lidx > 0 || $didx > 0) echo ",\n"; ?>
					'<?php echo str_replace(".", "/", $d); ?>'<?php } } echo "\n"; ?>
				]);
			</script>
		<?php } ?>
		<?php
			$scripts = theme("scripts", $request->theme);
			efault($scripts, array());
		?>
		<?php foreach ($scripts as $script) { ?>
			<script type="text/javascript" src="<?php echo uri("app/themes/".$request->theme."/public/js/$script"); ?>"></script>	
		<?php } ?>
	<?php } ?>
	<script type="text/javascript">
		var WEBSITE_URL = '<?php echo uri(); ?>';
	</script>
