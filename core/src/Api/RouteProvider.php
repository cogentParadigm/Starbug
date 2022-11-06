<?php
namespace Starbug\Core\Api;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\AdminImportsCollection;
use Starbug\Core\AdminMenusCollection;
use Starbug\Core\AdminTermsCollection;
use Starbug\Core\MenusTreeCollection;
use Starbug\Core\Operation\DeleteMenus;
use Starbug\Core\Operation\DeleteTerms;
use Starbug\Core\Routing\Route;
use Starbug\Core\SelectCollection;
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

    // Menus
    $this->addAdminApiRoute($api->addRoute("/menus/admin.{format:csv|json}"), "menus", AdminMenusCollection::class)
      ->onDelete(DeleteMenus::class);
    $this->addApiRoute($api->addRoute("/menus/select.json"), "menus", SelectCollection::class);
    $this->addApiRoute($api->addRoute("/menus/tree.json"), "menus", MenusTreeCollection::class);

    // Imports
    $this->addAdminApiRoute($api->addRoute("/imports/admin.{format:csv|json}"), "imports", AdminImportsCollection::class);
    $this->addApiRoute($api->addRoute("/imports/select.json"), "imports", SelectCollection::class);
    $this->addAdminApiRoute($api->addRoute("/imports-fields/admin.json"), "imports_fields");
    $this->addApiRoute($api->addRoute("/imports-fields/select.json"), "imports_fields", SelectCollection::class);
  }
}
