	<?php if (Etc::ENVIRONMENT == "production") { ?>
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-screen.css"); ?>" type="text/css" media="screen, projection">
			<!--<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-print.css"); ?>" type="text/css" media="print">-->
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$request->theme."-ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
	<?php } else { ?>
	
		<?php
			$styles = theme("styles", $request->theme);
			efault($styles['plugins'], array());
			efault($styles['screen'], array());
			efault($styles['print'], array());
			efault($styles['ie'], array());
			efault($styles['blueprint'], false);
			efault($styles['less'], false);
		?>
			
		<?php if ($styles['blueprint']) { ?>
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/reset.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/typography.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/forms.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/grid.css"); ?>" type="text/css" media="screen, projection">
			<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/print.css"); ?>" type="text/css" media="print">
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/src/ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		
		<?php if ($styles['less'] && file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-screen.less")) { ?>
			<link rel="stylesheet/less" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/custom-screen.less"); ?>" type="text/css" media="screen, projection">
		<?php } else if (file_exists(BASE_DIR."/app/themes/".$request->theme."/public/stylesheets/custom-screen.css")) { ?>
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
		<?php foreach ($styles['print'] as $print) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/styesheets/$print"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php foreach ($styles['ie'] as $ie) { ?>
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("app/themes/".$request->theme."/public/stylesheets/$ie"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php foreach ($styles['plugins'] as $plugin) { ?>
				<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/screen.css"); ?>" type="text/css" media="screen, projection">
				<?php if (file_exists(BASE_DIR."/core/app/public/stylesheets/plugins/$plugin/print.css")) { echo $plugin; ?>
					<link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/print.css"); ?>" type="text/css" media="print">
				<?php } ?>
				<?php if (file_exists(BASE_DIR."/core/app/public/stylesheets/plugins/$plugin/ie.css")) { ?>
					<!--[if IE]><link rel="stylesheet" href="<?php echo uri("core/app/public/stylesheets/plugins/$plugin/ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
				<?php } ?>
		<?php } ?>
		<?php if ($styles['less']) { ?>
			<script src="<?php echo uri("core/app/public/js/less-1.3.3.min.js"); ?>" type="text/javascript"></script>
		<?php } ?>
	<?php } ?>
