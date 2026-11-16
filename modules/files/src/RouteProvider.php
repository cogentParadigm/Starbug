<?php
namespace Starbug\Files;

use Starbug\Files\Controller\DownloadController;
use Starbug\Files\Controller\UploadController;
use Starbug\Routing\Route;
use Starbug\Routing\RouteProviderInterface;

class RouteProvider implements RouteProviderInterface {

  public function configure(Route $routes) {
    $routes->addRoute("upload", UploadController::class, ["groups" => "user"]);
    $routes->addRoute("files/download/{id:[0-9]+}", DownloadController::class, ["groups" => ["admin"]]);
  }
}
