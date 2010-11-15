<?php
	$sb->import("util/CSSParser");
	$conf = json_decode(file_get_contents(BASE_DIR."/etc/css.json"), true);
	$screen = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/reset.css",
		BASE_DIR."/core/app/public/stylesheets/src/typography.css",
		BASE_DIR."/core/app/public/stylesheets/src/forms.css",
		BASE_DIR."/core/app/public/stylesheets/src/grid.css",
		BASE_DIR."/var/public/stylesheets/screen.css"
	);
	if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $screen->add_plugin($plugin);
	if (!empty($conf['screen'])) foreach ($conf['screen'] as $custom) $screen->add_file(BASE_DIR."/$custom");
	$screen->parse();
	if (!empty($conf['classes'])) $screen->add_semantic_classes($conf['classes']);
	$screen->write();
	$print = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/print.css",
		BASE_DIR."/var/public/stylesheets/print.css"
	);
	if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $print->add_plugin($plugin);
	if (!empty($conf['print'])) foreach ($conf['print'] as $custom) $print->add_file(BASE_DIR."/$custom");
	$print->parse();
	if (!empty($conf['classes'])) $screen->add_semantic_classes($conf['classes']);
	$print->write();
	$ie = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/ie.css",
		BASE_DIR."/var/public/stylesheets/ie.css"
	);
	if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $ie->add_plugin($plugin);
	if (!empty($conf['ie'])) foreach ($conf['ie'] as $custom) $ie->add_file(BASE_DIR."/$custom");
	$ie->parse();
	if (!empty($conf['classes'])) $ie->add_semantic_classes($conf['classes']);
	$ie->write();
?>
