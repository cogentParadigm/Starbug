<?php
namespace Starbug\Css;
use Starbug\Core\Routing\RouteFilterInterface;
use Starbug\Core\RequestInterface;
class RouteFilter implements RouteFilterInterface {
	public function __construct(CssLoader $css, $theme) {
		$this->css = $css;
		$this->theme = $theme;
	}
	public function filterRoute($route, RequestInterface $request) {
		if (empty($route['theme'])) $route['theme'] = $this->theme;
		if (empty($route['layout'])) $route['layout'] = empty($route['type']) ? "views" : $route['type'];
		if (empty($route['template'])) $route['template'] = $request->getFormat();
		$this->css->setTheme($route['theme']);
		return $route;
	}
}
?>
