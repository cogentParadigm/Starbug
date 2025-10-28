<?php
namespace Starbug\Users;

use Starbug\Db\Collection\SelectCollection;
use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;
use Starbug\Routing\Controller\ViewController;
use Starbug\Users\Collection\AdminUsersCollection;
use Starbug\Users\Controller\LoginController;
use Starbug\Users\Display\ForgotPasswordForm;
use Starbug\Users\Display\PasswordResetForm;
use Starbug\Users\Display\UsersForm;
use Starbug\Users\Display\UsersGrid;
use Starbug\Users\Display\UsersSearchForm;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $users = $this->addCrudRoutes($routes->getRoute("admin")->addRoute("/users"), "users");
    $users->getRoute("/delete/{id:[0-9]+}")->onPost("Starbug\Core\Operation\SoftDelete");
    $users->setOptions([
      "grid" => UsersGrid::class,
      "form" => UsersForm::class,
      "searchForm" => UsersSearchForm::class
    ]);

    $api = $routes->getRoute("api");
    $this->addApiRoute($api->addRoute("/groups/select.json"), "groups", SelectCollection::class);

    $api->addRoute("/users/admin.{format:csv|json}", "Starbug\Core\ApiUsersController", [
      "collection" => AdminUsersCollection::class,
      "model" => "users"
    ])
    ->onPost("Starbug\Core\Operation\Save")
    ->onDelete("Starbug\Core\Operation\SoftDelete");
    $api->addRoute("/users/select.json", "Starbug\Core\ApiUsersController", ["collection" => "Select"]);

    $routes->addRoute("login", [LoginController::class, "defaultAction"])
      ->onPost("Starbug\Users\Operation\Login");

    $routes->addRoute("logout", [LoginController::class, "logout"])
      ->onPost("Starbug\Users\Operation\Logout");

    $routes->addRoute("forgot-password", ViewController::class, [
      "view" => "forgot-password.html",
      "form" => ForgotPasswordForm::class,
      "successUrl" => "forgot-password/submitted"
    ])->onPost("Starbug\Users\Operation\ForgotPassword");

    $routes->addRoute("forgot-password/submitted", ViewController::class, [
      "view" => "forgot-password/submitted.html"
    ]);

    $routes->addRoute("reset-password", ViewController::class, [
      "view" => "reset-password.html",
      "form" => PasswordResetForm::class,
      "successUrl" => "reset-password/submitted"
    ])->onPost("Starbug\Users\Operation\ResetPassword");

    $routes->addRoute("reset-password/submitted", ViewController::class, [
      "view" => "reset-password/submitted.html"
    ]);
  }
}
