<?php
namespace Starbug\Users;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $this->addCrudRoutes($routes->getRoute("admin")->addRoute("/users"), "users");

    $routes->addRoute("login", ["Starbug\Users\LoginController", "defaultAction"], [
      "operation" => "Starbug\Users\Operation\Login"
    ]);

    $routes->addRoute("logout", ["Starbug\Users\LoginController", "logout"], [
      "operation" => "Starbug\Users\Operation\Logout"
    ]);

    $routes->addRoute("forgot-password", ["Starbug\Users\LoginController", "forgotPassword"], [
      "operation" => "Starbug\Users\Operation\ForgotPassword"
    ]);

    $routes->addRoute("reset-password", ["Starbug\Users\LoginController", "resetPassword"], [
      "operation" => "Starbug\Users\Operation\ResetPassword"
    ]);
  }
}
