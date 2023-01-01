<?php
namespace Starbug\Imports;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;
use Starbug\Imports\Admin\ImportsCollection;
use Starbug\Imports\Admin\ImportsFieldsForm;
use Starbug\Imports\Admin\ImportsForm;
use Starbug\Imports\Admin\ImportsGrid;
use Starbug\Imports\Admin\ImportsTransformersForm;
use Starbug\Imports\Admin\ImportsTransformersSelectCollection;
use Starbug\Imports\Operation\Run;
use Starbug\Imports\Operation\SaveTransformer;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $imports = $this->addCrud($routes, "imports");
    $imports["list"]->setOptions([
      "grid" => ImportsGrid::class,
      "form" => ImportsForm::class
    ]);
    $imports["adminApi"]->setOption("collection", ImportsCollection::class);

    $imports["create"]->resolve("formParams", function (ServerRequestInterface $request) {
      return ["model" => $request->getQueryParams()["model"]];
    });
    $imports["create"]->setOption("successUrl", "admin/imports/update/{{ row.id }}");
    $imports["create"]->resolve("row", "Starbug\Core\Routing\Resolvers\RowByInsertId", "outbound");

    $imports["update"]->setOption("successUrl", "admin/{{ row.model }}/import");
    $imports["update"]->resolve("row", "Starbug\Core\Routing\Resolvers\RowById");

    $imports["list"]->addRoute("/run/{id:[0-9]+}", "Starbug\Core\Controller\ViewController", [
      "view" => "admin/update.html",
      "form_header" => "Run Import",
      "action" => "run"
    ])
    ->onPost(Run::class);

    $fields = $this->addCrud($routes, "imports_fields");
    $fields["list"]->setOption("form", ImportsFieldsForm::class);

    $transformers = $this->addCrud($routes, "imports_transformers");
    $transformers["list"]->setOption("form", ImportsTransformersForm::class);
    $transformers["create"]->onPost(SaveTransformer::class);
    $transformers["update"]->onPost(SaveTransformer::class);
    $transformers["selectApi"]->setOption("collection", ImportsTransformersSelectCollection::class);
  }
}
