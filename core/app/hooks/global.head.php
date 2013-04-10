		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<title><?php echo strip_tags($request->payload['title']).' - '.settings("site_name"); ?></title>
		<meta name="description" content="<?php echo $request->payload['description']; ?>"/>
		<?php echo $request->payload['meta']; ?>
		<base href="<?php echo uri(); ?>"/>
		<?php if (!empty($request->payload['canonical'])) { ?><link rel="canonical" href="<?php echo $request->payload['canonical']; ?>"/><?php } ?>
		<?php echo settings("meta"); ?>
