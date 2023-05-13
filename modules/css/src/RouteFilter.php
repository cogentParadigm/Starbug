<?php
namespace Starbug\Css;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Routing\Route;
use Starbug\Routing\RouteFilterInterface;

class RouteFilter implements RouteFilterInterface {
  public function __construct(CssLoader $css, $theme) {
    $this->css = $css;
    $this->theme = $theme;
  }
  public function filterRoute(Route $route, ServerRequestInterface $request) {
    if (!$route->hasOption("theme")) {
      $route->setOption("theme", $this->theme);
    }
    if (!$route->hasOption("layout")) {
      $route->setOption("layout", "views");
    }
    $this->css->setTheme($route->getOption("theme"));
    return $route;
  }
}
