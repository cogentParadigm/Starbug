<?php
namespace Starbug\Files;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $routes->addRoute("upload", "Starbug\Files\UploadController", ["groups" => "user"]);
    $routes->addRoute("files/download/{id:[0-9]+}", "Starbug\Files\DownloadController", ["groups" => ["admin"]]);

    $admin = $routes->getRoute("admin");
    $admin->addRoute("/media", "Starbug\Core\Controller\ViewController", [
      "view" => "media-browser.html",
      "template" => false
    ]);
    $admin->addRoute("/media/update/{id:[0-9]+}", "Starbug\Core\Crud\UpdateController", [
      "model" => "files",
      "action" => "update"
    ]);

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/files/admin.json"), "files", "AdminFiles")
      ->setController("Starbug\Files\ApiFilesController");
    $this->addApiRoute($api->addRoute("/files/select.json"), "files", "FilesSelect")
      ->setController("Starbug\Files\ApiFilesController");
  }
}
