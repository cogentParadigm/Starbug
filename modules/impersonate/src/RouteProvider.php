<?php
namespace Starbug\Users\Impersonate;

use Starbug\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Resolvers\RowById;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $users = $routes->getRoute("admin")->getRoute("/users");

    $users->addRoute("/impersonate/{id:[0-9]+}", ViewController::class, [
      "view" => "admin/users/impersonate.html",
      "form" => Form::class,
      "successUrl" => ""
    ])
    ->resolve("row", RowById::class)
    ->onPost(Login::class);

    $routes->getRoute("logout")->setController(Logout::class);
  }
}
