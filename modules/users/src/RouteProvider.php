<?php
namespace Starbug\Users;

use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Route;
use Starbug\Routing\RouteProviderInterface;
use Starbug\Users\Controller\LoginController;
use Starbug\Users\Display\ForgotPasswordForm;
use Starbug\Users\Display\PasswordResetForm;

class RouteProvider implements RouteProviderInterface {

  public function configure(Route $routes) {
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

    $routes->addRoute("profile", ViewController::class, [
      "view" => "profile.html",
      "groups" => "user"
    ])
    ->resolve("id", "Starbug\Routing\Resolvers\UserId")
    ->onPost("Starbug\Users\Operation\UpdateProfile");
  }
}
