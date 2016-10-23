<?php
namespace Starbug\Css;
use Starbug\Core\ConfigInterface;
use Starbug\Core\ResourceLocatorInterface;
use Starbug\Core\CSSParser;
class CssGenerateCommand {
	public $dirs = array();
	public $generate = array();
	public $copy = array();
	public function __construct($base_directory, ConfigInterface $config, CssLoader $css, ResourceLocatorInterface $locator) {
		$this->base_directory = $base_directory;
		$this->config = $config;
		$this->css = $css;
		$this->locator = $locator;
	}
	public function run($params) {
		$themes = $this->config->get("themes");
		foreach ($themes as $name) {
			$this->css->setTheme($name);
			$config = $this->css->getConfiguration();
			foreach ($config as $media => $styles) {
				$parser = new CSSParser($this->base_directory."/var/public/stylesheets/".$name."-".$media.".css");
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
?>
