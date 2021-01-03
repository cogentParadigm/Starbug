<?php
namespace Starbug\Css;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Routing\RouteFilterInterface;

class RouteFilter implements RouteFilterInterface {
  public function __construct(CssLoader $css, $theme) {
    $this->css = $css;
    $this->theme = $theme;
  }
  public function filterRoute($route, ServerRequestInterface $request) {
    if (empty($route['theme'])) $route['theme'] = $this->theme;
    if (empty($route['layout'])) $route['layout'] = empty($route['type']) ? "views" : $route['type'];
    $this->css->setTheme($route['theme']);
    return $route;
  }
}
