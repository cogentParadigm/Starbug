<?php
namespace Starbug\Core\Api;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\AdminTermsCollection;
use Starbug\Core\Operation\DeleteTerms;
use Starbug\Core\Routing\Route;
use Starbug\Core\SelectTermsCollection;
use Starbug\Core\TermsListCollection;
use Starbug\Core\TermsTreeCollection;

class RouteProvider extends AdminRouteProvider {
  public function configure(Route $routes) {
    // $routes->addRoute("api/{controller}/{action}.{format}", "Starbug\\Core\\ApiRoutingController");
    $api = $routes->addRoute("api", null, ["groups" => "admin"]);

    // Taxonomy
    $this->addAdminApiRoute($api->addRoute("/terms/admin.{format:csv|json}"), "terms", AdminTermsCollection::class)
      ->onDelete(DeleteTerms::class);
    $this->addApiRoute($api->addRoute("/terms/select.json"), "terms", SelectTermsCollection::class);
    $this->addApiRoute($api->addRoute("/terms/index.json"), "terms", TermsListCollection::class);
    $this->addApiRoute($api->addRoute("/terms/tree.json"), "terms", TermsTreeCollection::class);
  }
}
