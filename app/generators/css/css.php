<?php
	include(BASE_DIR."/core/script/CSSParser.php");
	$conf = json_decode(file_get_contents(BASE_DIR."/etc/css.json"), true);
	$screen = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/reset.css",
		BASE_DIR."/core/app/public/stylesheets/src/typography.css",
		BASE_DIR."/core/app/public/stylesheets/src/forms.css",
		BASE_DIR."/core/app/public/stylesheets/src/grid.css",
		BASE_DIR."/core/app/public/stylesheets/screen.css"
	);
	if (!empty($conf['screen']['plugins'])) foreach ($conf['screen']['plugins'] as $plugin) $screen->add_plugin($plugin);
	if (!empty($conf['screen']['custom'])) foreach ($conf['screen']['custom'] as $custom) $screen->add_file(BASE_DIR."/$custom");
	$screen->parse();
	if (!empty($conf['screen']['classes'])) $screen->add_semantic_classes($conf['screen']['classes']);
	$screen->write();
	$print = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/print.css",
		BASE_DIR."/core/app/public/stylesheets/print.css"
	);
	if (!empty($conf['print']['plugins'])) foreach ($conf['print']['plugins'] as $plugin) $print->add_plugin($plugin);
	if (!empty($conf['print']['custom'])) foreach ($conf['print']['custom'] as $custom) $print->add_file(BASE_DIR."/$custom");
	$print->parse();
	if (!empty($conf['print']['classes'])) $screen->add_semantic_classes($conf['print']['classes']);
	$print->write();
	$ie = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/ie.css",
		BASE_DIR."/core/app/public/stylesheets/ie.css"
	);
	if (!empty($conf['ie']['plugins'])) foreach ($conf['ie']['plugins'] as $plugin) $ie->add_plugin($plugin);
	if (!empty($conf['ie']['custom'])) foreach ($conf['ie']['custom'] as $custom) $ie->add_file(BASE_DIR."/$custom");
	$ie->parse();
	if (!empty($conf['ie']['classes'])) $ie->add_semantic_classes($conf['ie']['classes']);
	$ie->write();
?>
