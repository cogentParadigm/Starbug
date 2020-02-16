<?php
namespace Starbug\Core\Routing;

class RoutesHelper {
  public static function adminRoute($controller, $route = []) {
    return $route + [
      "title" => "Admin",
      "groups" => "admin",
      "theme" => "storm",
      "controller" => $controller
    ];
  }
  public static function crudRoutes($path, $controller) {
    return [
      $path => static::adminRoute($controller),
      $path . "/create" => static::adminRoute($controller, ["action" => "create"]),
      $path . "/update/{id:[0-9]+}" => static::adminRoute($controller, ["action" => "update"])
    ];
  }
  public static function crudiRoutes($path, $controller) {
    return static::crudRoutes($path, $controller) + [
      $path . "/import" => static::adminRoute($controller, ["action" => "import"])
    ];
  }
}
