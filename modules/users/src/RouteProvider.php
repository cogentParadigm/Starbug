<?php
namespace Starbug\Users;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $users = $this->addCrudRoutes($routes->getRoute("admin")->addRoute("/users"), "users");
    $users->getRoute("/delete/{id:[0-9]+}")->onPost("Starbug\Core\Operation\SoftDelete");

    $api = $routes->getRoute("api");
    $api->addRoute("/users/admin.{format:csv|json}", "Starbug\Core\ApiUsersController", ["collection" => "AdminUsers"])
    ->onPost("Starbug\Core\Operation\Save")
    ->onDelete("Starbug\Core\Operation\SoftDelete");
    $api->addRoute("/users/select.json", "Starbug\Core\ApiUsersController", ["collection" => "Select"]);

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
