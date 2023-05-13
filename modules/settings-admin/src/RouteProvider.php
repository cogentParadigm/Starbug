<?php
namespace Starbug\Settings\Admin;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");

    $admin->addRoute("/settings", ViewController::class, [
      "view" => "settings.html",
      "model" => "settings",
      "form" => SettingsForm::class
    ])->onPost(SaveSettings::class);

    $this->addCrud($routes, "settings_categories");
  }
}
