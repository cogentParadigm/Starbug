<?php
namespace Starbug\Files;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;
use Starbug\Files\Collection\AdminFilesCollection;
use Starbug\Files\Collection\FilesSelectCollection;
use Starbug\Files\Controller\ApiFilesController;
use Starbug\Files\Controller\DownloadController;
use Starbug\Files\Controller\UploadController;
use Starbug\Files\Display\FilesForm;
use Starbug\Routing\Controller\ViewController;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $routes->addRoute("upload", UploadController::class, ["groups" => "user"]);
    $routes->addRoute("files/download/{id:[0-9]+}", DownloadController::class, ["groups" => ["admin"]]);

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

    $adminFiles = $this->addCrud($routes, "files", [
      "form" => FilesForm::class
    ]);
    $adminFiles["adminApi"]->setController(ApiFilesController::class)->setOption("collection", AdminFilesCollection::class);
    $adminFiles["selectApi"]->setController(ApiFilesController::class)->setOption("collection", FilesSelectCollection::class);
  }
}
