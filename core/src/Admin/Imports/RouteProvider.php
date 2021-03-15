<?php
namespace Starbug\Core\Admin\Imports;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $imports = $this->addCrudRoutes($admin->addRoute("/imports"), "imports");

    $create = $imports->getRoute("/create");
    $create->setOption("successUrl", "admin/imports/update/{{ row.id }}");
    $create->resolve("row", "Starbug\Core\Routing\Resolvers\RowByInsertId", "outbound");

    $update = $imports->getRoute("/update/{id:[0-9]+}");
    $update->setOption("successUrl", "admin/{{ row.model }}/import");
    $update->resolve("row", "Starbug\Core\Routing\Resolvers\RowById");

    $imports->addRoute("/run/{id:[0-9]+}", "Starbug\Core\Controller\ViewController", [
      "view" => "admin/update.html",
      "form_header" => "Run Import",
      "action" => "run"
    ])
    ->onPost("Starbug\Core\Admin\Imports\Run");

    $this->addCrudRoutes($admin->addRoute("/imports-fields"), "imports_fields");
  }
}
