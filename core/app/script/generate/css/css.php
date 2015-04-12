<?php
class CssGenerateCommand {
	public $dirs = array();
	public $generate = array();
	public $copy = array();
	public function __construct($base_directory, ConfigInterface $config) {
		$this->base_directory = $base_directory;
		$this->config = $config;
	}
	public function run($params) {
		$themes = $this->config->get("themes");
		foreach ($themes as $name) {
			$conf = $this->config->get("info.styles", "themes/".$name);
			efault($conf['less'], false);

			/******************SCREEN********************/
			$screen = new CSSParser($this->base_directory."/var/public/stylesheets/$name-screen.css");

			//compile custom-screen.less
			if ($conf['less'] && file_exists($this->base_directory."/app/themes/".$name."/public/stylesheets/custom-screen.less")) {
				exec("lessc ".$this->base_directory."/app/themes/".$name."/public/stylesheets/custom-screen.less ".$this->base_directory."/app/themes/".$name."/public/stylesheets/custom-screen.css");
			}
			//add custom-screen.css
			if (file_exists($this->base_directory."/app/themes/".$name."/public/stylesheets/custom-screen.css")) $screen->add_file($this->base_directory."/app/themes/".$name."/public/stylesheets/custom-screen.css", "$name custom-screen.css");
			//add additional screen styles
			if (!empty($conf['screen'])) foreach ($conf['screen'] as $custom) $screen->add_file($this->base_directory."/app/themes/".$name."/public/stylesheets/$custom");
			//add plugins
			if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $screen->add_plugin($plugin);
			$screen->write();

			/******************PRINT********************/
			$print = new CSSParser($this->base_directory."/var/public/stylesheets/$name-print.css");

			//add custom-print.css
			if (file_exists($this->base_directory."app/themes/".$name."/public/stylesheets/custom-print.css")) $print->add_file($this->base_directory."/app/themes/".$name."/public/stylesheets/custom-print.css");
			//add additional print styles
			if (!empty($conf['print'])) foreach ($conf['print'] as $custom) $print->add_file($this->base_directory."/app/themes/".$name."/public/stylesheets/$custom");
			//add plugins
			if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $print->add_plugin($plugin);
			$print->write();

			/******************IE********************/
			$ie = new CSSParser($this->base_directory."/var/public/stylesheets/$name-ie.css");

			//add custom-ie.css
			if (file_exists($this->base_directory."app/themes/".$name."/public/stylesheets/custom-ie.css")) $ie->add_file($this->base_directory."/app/themes/".$name."/public/stylesheets/custom-ie.css");
			//add additional ie styles
			if (!empty($conf['ie'])) foreach ($conf['ie'] as $custom) $ie->add_file($this->base_directory."/app/themes/".$name."/public/stylesheets/$custom");
			//add plugins
			if (!empty($conf['plugins'])) foreach ($conf['plugins'] as $plugin) $ie->add_plugin($plugin);
			$ie->write();
		}
	}
}
?>
