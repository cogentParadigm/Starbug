<?php
namespace Starbug\Css;
use Starbug\Core\ConfigInterface;
use Starbug\Core\ResourceLocatorInterface;
class CssBuildCommand {
	public function __construct($base_directory, ConfigInterface $config, CssLoader $css, ResourceLocatorInterface $locator) {
		$this->base_directory = $base_directory;
		$this->config = $config;
		$this->css = $css;
		$this->locator = $locator;
	}
	public function run($argv) {
		echo "This command is deprecated. Use 'grunt build', 'grunt css', or 'grunt watch'.\n";
		return;
		$themes = $this->config->get("themes");
		foreach ($themes as $name) {
			$this->css->setTheme($name);
			$config = $this->css->getDevelopmentConfiguration();
			foreach ($config as $media => $styles) {
				$parser = new CssParser($this->base_directory, "var/public/stylesheets/".$name."-".$media.".css");
				foreach ($styles as $idx => $style) {
					echo $style["href"]."\n";
					if ($style["rel"] == "stylesheet/less") {
						$css = str_replace(".less", ".css", $style["href"]);
						exec("lessc ".$this->base_directory."/".$style["href"]." ".$this->base_directory."/".$css);
						$style["href"] = $css;
					}
					$parser->add_file($this->base_directory."/".$style["href"]);
				}
				$parser->write();
			}
		}
	}
}
