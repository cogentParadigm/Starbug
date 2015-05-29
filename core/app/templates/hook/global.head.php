		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<title><?php echo strip_tags($response->title).' - '.$sb->settings->get("site_name"); ?></title>
		<meta name="description" content="<?php echo $response->description; ?>"/>
		<?php echo $response->meta; ?>
		<base href="<?php echo uri(); ?>"/>
		<?php if (!empty($response->canonical)) { ?><link rel="canonical" href="<?php echo $response->canonical; ?>"/><?php } ?>
		<?php echo $sb->settings->get("meta"); ?>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
