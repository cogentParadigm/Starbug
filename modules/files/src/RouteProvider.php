<?php
namespace Starbug\Files;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;
use Starbug\Files\Collection\AdminFilesCollection;
use Starbug\Files\Collection\FilesSelectCollection;
use Starbug\Files\Controller\ApiFilesController;
use Starbug\Files\Controller\DownloadController;
use Starbug\Files\Controller\UploadController;
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

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/files/admin.{format:csv|json}"), "files", AdminFilesCollection::class)
      ->setController(ApiFilesController::class);
    $this->addApiRoute($api->addRoute("/files/select.json"), "files", FilesSelectCollection::class)
      ->setController(ApiFilesController::class);
  }
}
