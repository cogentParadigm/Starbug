	<?php if (Etc::ENVIRONMENT == "production") { ?>
			<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$response->theme."-screen.css"); ?>" type="text/css" media="screen, projection">
			<!--<link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$response->theme."-print.css"); ?>" type="text/css" media="print">-->
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("var/public/stylesheets/".$response->theme."-ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
	<?php } else { ?>

		<?php
			$styles = theme("styles", $response->theme);
			efault($styles['screen'], array());
			efault($styles['print'], array());
			efault($styles['ie'], array());
			efault($styles['less'], false);
		?>

		<?php if ($styles['less'] && file_exists(BASE_DIR."/app/themes/".$response->theme."/public/stylesheets/custom-screen.less")) { ?>
			<link rel="stylesheet/less" href="<?php echo uri("app/themes/".$response->theme."/public/stylesheets/custom-screen.less"); ?>" type="text/css" media="screen, projection">
		<?php } else if (file_exists(BASE_DIR."/app/themes/".$response->theme."/public/stylesheets/custom-screen.css")) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$response->theme."/public/stylesheets/custom-screen.css"); ?>" type="text/css" media="screen, projection">
		<?php } ?>

		<?php if (file_exists(BASE_DIR."/app/themes/".$response->theme."/public/stylesheets/custom-print.css")) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$response->theme."/public/stylesheets/custom-print.css"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php if (file_exists(BASE_DIR."/app/themes/".$response->theme."/public/stylesheets/custom-ie.css")) { ?>
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("app/themes/".$response->theme."/public/stylesheets/custom-ie.css"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php foreach ($styles['screen'] as $screen) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$response->theme."/public/stylesheets/$screen"); ?>" type="text/css" media="screen, projection">
		<?php } ?>
		<?php foreach ($styles['print'] as $print) { ?>
			<link rel="stylesheet" href="<?php echo uri("app/themes/".$response->theme."/public/styesheets/$print"); ?>" type="text/css" media="print">
		<?php } ?>
		<?php foreach ($styles['ie'] as $ie) { ?>
			<!--[if IE]><link rel="stylesheet" href="<?php echo uri("app/themes/".$response->theme."/public/stylesheets/$ie"); ?>" type="text/css" media="screen, projection"><![endif]-->
		<?php } ?>
		<?php if ($styles['less']) { ?>
			<script type="text/javascript">
				less = { env: 'development' };
			</script>
			<script src="<?php echo uri("libraries/less-1.7.0.min.js"); ?>" type="text/javascript"></script>
		<?php } ?>
	<?php } ?>
