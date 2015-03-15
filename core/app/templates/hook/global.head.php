		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<title><?php echo strip_tags($request->title).' - '.settings("site_name"); ?></title>
		<meta name="description" content="<?php echo $request->description; ?>"/>
		<?php echo $request->meta; ?>
		<base href="<?php echo uri(); ?>"/>
		<?php if (!empty($request->canonical)) { ?><link rel="canonical" href="<?php echo $request->canonical; ?>"/><?php } ?>
		<?php echo settings("meta"); ?>
