	<?php if (Etc::ENVIRONMENT == "production") { ?>

		<link rel="stylesheet" href="<?php echo $this->url->build("var/public/stylesheets/".$response->theme."-screen.css"); ?>" type="text/css" media="screen, projection">
	<?php } else { ?>

		<?php
			echo implode("\n", $this->css->getStylesheets());
			$styles = $this->config->get("info.styles", 'themes/'.$response->theme);
			if (empty($styles['less'])) $styles['less'] = false;
		?>
		<?php if ($styles['less']) { ?>

		<script type="text/javascript">
				less = { env: 'development' };
		</script>
		<script src="<?php echo $this->url->build("libraries/less/dist/less.min.js"); ?>" type="text/javascript"></script>
		<?php } ?>
	<?php } ?>
