<?php
	$sb->import("core/lib/CSSParser");
	$themes = config("themes");
	foreach ($themes as $name) {
		
		$conf = theme("styles", $name);
		efault($conf['blueprint'], false);
		efault($conf['less'], false);
		
		/******************SCREEN********************/
		if ($conf['blueprint']) {
			$screen = new CSSParser(
				BASE_DIR."/core/app/public/stylesheets/src/reset.css",
				BASE_DIR."/core/app/public/stylesheets/src/typography.css",
				BASE_DIR."/core/app/public/stylesheets/src/forms.css",
				BASE_DIR."/core/app/public/stylesheets/src/grid.css",
				BASE_DIR."/var/public/stylesheets/$name-screen.css"
			);
		} else {
			$screen = new CSSParser(BASE_DIR."/var/public/stylesheets/$name-screen.css");			
		}
		//compile custom-screen.less
		if ($conf['less'] && file_exists(BASE_DIR."/app/themes/".$name."/public/stylesheets/custom-screen.less")) {
			exec("lessc ".BASE_DIR."/app/themes/".$name."/public/stylesheets/custom-screen.less ".BASE_DIR."/app/themes/".$name."/public/stylesheets/custom-screen.css");
		}
		//add custom-screen.css
		if (file_exists(BASE_DIR."/app/themes/".$name."/public/stylesheets/custom-screen.css")) $screen->add_file(BASE_DIR."/app/themes/".$name."/public/stylesheets/custom-screen.css", "$name custom-screen.css");
		//add additional screen styles
		if (!empty($conf['screen'])) foreach ($conf['screen'] as $custom) $screen->add_file(BASE_DIR."/app/themes/".$name."/public/stylesheets/$custom");
		//add plugins
		if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $screen->add_plugin($plugin);
		$screen->parse();
		$screen->write();
		
		/******************PRINT********************/
		if ($conf['blueprint']) {
			$print = new CSSParser(
				BASE_DIR."/core/app/public/stylesheets/src/print.css",
				BASE_DIR."/var/public/stylesheets/$name-print.css"
			);
		} else {
			$print = new CSSParser(BASE_DIR."/var/public/stylesheets/$name-print.css");
		}
		//add custom-print.css
		if (file_exists(BASE_DIR."app/themes/".$name."/public/stylesheets/custom-print.css")) $print->add_file(BASE_DIR."/app/themes/".$name."/public/stylesheets/custom-print.css");
		//add additional print styles
		if (!empty($conf['print'])) foreach ($conf['print'] as $custom) $print->add_file(BASE_DIR."/app/themes/".$name."/public/stylesheets/$custom");
		//add plugins
		if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $print->add_plugin($plugin);
		$print->parse();
		$print->write();

		/******************IE********************/
		if ($conf['blueprint']) {
			$ie = new CSSParser(
				BASE_DIR."/core/app/public/stylesheets/src/ie.css",
				BASE_DIR."/var/public/stylesheets/$name-ie.css"
			);
		} else {
			$ie = new CSSParser(BASE_DIR."/var/public/stylesheets/$name-ie.css");
		}
		//add custom-ie.css
		if (file_exists(BASE_DIR."app/themes/".$name."/public/stylesheets/custom-ie.css")) $ie->add_file(BASE_DIR."/app/themes/".$name."/public/stylesheets/custom-ie.css");
		//add additional ie styles
		if (!empty($conf['ie'])) foreach ($conf['ie'] as $custom) $ie->add_file(BASE_DIR."/app/themes/".$name."/public/stylesheets/$custom");
		//add plugins
		if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $ie->add_plugin($plugin);
		$ie->parse();
		$ie->write();
	}
?>
