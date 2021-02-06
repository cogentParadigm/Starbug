<?php
namespace Starbug\Users;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $users = $this->addCrudRoutes($routes->getRoute("admin")->addRoute("/users"), "users");

    $users->getRoute("/delete/{id:[0-9]+}")->onPost("Starbug\Core\Operation\SoftDelete");

    $routes->addRoute("login", ["Starbug\Users\LoginController", "defaultAction"])
      ->onPost("Starbug\Users\Operation\Login");

    $routes->addRoute("logout", ["Starbug\Users\LoginController", "logout"])
      ->onPost("Starbug\Users\Operation\Logout");

    $routes->addRoute("forgot-password", ["Starbug\Users\LoginController", "forgotPassword"])
      ->onPost("Starbug\Users\Operation\ForgotPassword");

    $routes->addRoute("reset-password", ["Starbug\Users\LoginController", "resetPassword"])
      ->onPost("Starbug\Users\Operation\ResetPassword");
  }
}
