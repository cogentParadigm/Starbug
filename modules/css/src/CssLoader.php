<?php
namespace Starbug\Css;
use Starbug\Core\ResourceLocatorInterface;
use Starbug\Core\URLInterface;
use Starbug\Core\ResponseInterface;
class CssLoader {
	protected $options = false;
	protected $less = false;
	public function __construct(ResourceLocatorInterface $locator, URLInterface $url, ResponseInterface $response, $modules, $environment) {
		$this->locator = $locator;
		$this->url = $url;
		$this->response = $response;
		$this->modules = $modules;
		$this->environment = $environment;
	}
	public function getConfiguration($reload = false) {
		$this->load($reload);
		return $this->options;
	}
	public function getStylesheets() {
		$this->load();
		$stylesheets = [];
		foreach ($this->options as $media => $styles) {
			foreach ($styles as $style) {
				$stylesheets[] = '<link '.
					'rel="'.$style["rel"].'" '.
					'href="'.$this->url->build($style["href"]).'" '.
					'type="text/css" '.
					'media="'.$media.'">';
			}
		}
		return $stylesheets;
	}
	public function setTheme($name) {
		$this->modules["Starbug\Theme"] = "app/themes/".$name;
		$this->locator->set("Starbug\Theme", "app/themes/".$name);
		$this->response->theme = $name;
		$this->options = false;
	}
	protected function load($reload = false) {
		if (false === $this->options || true == $reload) {
			if ($this->environment == "production") {
				$this->loadProduction();
			} else {
				$this->loadDevelopment();
			}
		}
	}
	protected function loadProduction() {
		$this->less = false;
		$this->options = [
			"screen" => [
				["rel" => "stylesheet", "href" => "var/public/stylesheets/".$this->response->theme."-screen.css"]
			]
		];
	}
	protected function loadDevelopment() {
		$this->less = false;
		$this->options = [];
		$resources = $this->locator->locate("stylesheets.json", "etc");
		$resources = array_reverse($resources);
		foreach ($resources as $mid => $resource) {
			$stylesheets = json_decode(file_get_contents($resource), true);
			foreach ($stylesheets as $media => $styles) {
				foreach ($styles as $style) {
					if (!is_array($style)) {
						$style = ["href" => $style];
					}
					if (empty($style["rel"])) $style["rel"] = "stylesheet";
					else if ($style["rel"] == "stylesheet/less") $this->less = true;
					$style["href"] = $this->modules[$mid] . "/" . $style["href"];
					$this->options[$media][] = $style;
				}
			}
		}
	}
	public function hasLess() {
		return $this->less;
	}
}
?>
