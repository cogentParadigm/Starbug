<?php
namespace Starbug\Files\Admin;

use Starbug\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Files\Admin\Collection\AdminFilesCollection;
use Starbug\Files\Admin\Display\FilesForm;
use Starbug\Files\Admin\Collection\FilesSelectCollection;
use Starbug\Files\Admin\Controller\ApiFilesController;
use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $admin->addRoute("/media", ViewController::class, [
      "view" => "media-browser.html",
      "template" => false
    ]);
    $admin->addRoute("/media/update/{id:[0-9]+}", ViewController::class, [
      "view" => "admin/update.html",
      "model" => "files",
      "action" => "update"
    ]);

    $files = $this->addCrud($routes, "files", [
      "form" => FilesForm::class
    ]);
    $files["adminApi"]->setController(ApiFilesController::class)->setOption("collection", AdminFilesCollection::class);
    $files["selectApi"]->setController(ApiFilesController::class)->setOption("collection", FilesSelectCollection::class);
  }
}
