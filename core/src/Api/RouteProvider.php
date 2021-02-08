<?php
namespace Starbug\Core\Api;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {
  public function configure(Route $routes) {
    // $routes->addRoute("api/{controller}/{action}.{format}", "Starbug\\Core\\ApiRoutingController");
    $api = $routes->addRoute("api", null, ["groups" => "admin"]);

    // Taxonomy
    $this->addAdminApiRoute($api->addRoute("/terms/admin.json"), "terms", "AdminTerms");
    $this->addApiRoute($api->addRoute("/terms/select.json"), "terms", "SelectTerms");
    $this->addApiRoute($api->addRoute("/terms/index.json"), "terms", "TermsList");
    $this->addApiRoute($api->addRoute("/terms/tree.json"), "terms", "TermsTree");

    // Menus
    $this->addAdminApiRoute($api->addRoute("/menus/admin.json"), "menus", "AdminMenus");
    $this->addApiRoute($api->addRoute("/menus/select.json"), "menus", "Select");
    $this->addApiRoute($api->addRoute("/menus/tree.json"), "menus", "MenusTree");

    // Imports
    $this->addAdminApiRoute($api->addRoute("/imports/admin.json"), "imports", "AdminImports");
    $this->addApiRoute($api->addRoute("/imports/select.json"), "imports", "Select");
    $this->addAdminApiRoute($api->addRoute("/imports-fields/admin.json"), "imports_fields");
    $this->addApiRoute($api->addRoute("/imports-fields/select.json"), "imports_fields", "Select");
  }
}
