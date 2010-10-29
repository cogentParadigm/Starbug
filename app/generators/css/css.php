<?php
	include(BASE_DIR."/core/script/CSSParser.php");
	include(BASE_DIR."/app/public/stylesheets/classes.php");
	$screen = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/reset.css",
		BASE_DIR."/core/app/public/stylesheets/src/typography.css",
		BASE_DIR."/core/app/public/stylesheets/src/forms.css",
		BASE_DIR."/core/app/public/stylesheets/src/grid.css",
		BASE_DIR."/core/app/public/stylesheets/screen.css"
	);
	$screen->add_plugin("buttons");
	$screen->add_plugin("rounding");
	$screen->add_file(BASE_DIR."/app/public/stylesheets/custom-screen.css");
	$screen->parse();
	$screen->add_semantic_classes($classes);
	$screen->write();
	$print = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/print.css",
		BASE_DIR."/app/public/stylesheets/custom-print.css",
		BASE_DIR."/core/app/public/stylesheets/print.css"
	);
	$print->parse();
	$print->write();
	$ie = new CSSParser(
		BASE_DIR."/core/app/public/stylesheets/src/ie.css",
		BASE_DIR."/app/public/stylesheets/custom-ie.css",
		BASE_DIR."/core/app/public/stylesheets/ie.css"
	);
	$ie->parse();
	$ie->write();
?>
