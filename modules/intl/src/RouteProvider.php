<?php
namespace Starbug\Intl;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;
use Starbug\Intl\Collection\ProvincesSelectCollection;
use Starbug\Intl\Collection\SelectAddressCollection;
use Starbug\Intl\Controller\AddressController;
use Starbug\Intl\Display\CountriesForm;
use Starbug\Intl\Display\CountriesGrid;
use Starbug\Intl\Display\ProvincesForm;
use Starbug\Intl\Display\ProvincesGrid;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $address = $this->addCrudRoutes($routes->addRoute("address"), "address");
    $address->setController(null); // no list
    $address->setOption("groups", "user");
    $address->addRoute("/form[/{locale}]", AddressController::class, ["format" => "xhr"]);

    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/countries"), "countries")->setOptions([
      "grid" => CountriesGrid::class,
      "form" => CountriesForm::class
    ]);
    $this->addCrudRoutes($admin->addRoute("/provinces"), "provinces")->setOptions([
      "grid" => ProvincesGrid::class,
      "form" => ProvincesForm::class
    ]);

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/address/admin.{format:csv|json}"), "address");
    $this->addApiRoute($api->addRoute("/address/select.json"), "address", SelectAddressCollection::class);

    $this->addAdminApiRoute($api->addRoute("/countries/admin.{format:csv|json}"), "countries");
    $this->addApiRoute($api->addRoute("/countries/select.json"), "countries", "Select");

    $this->addAdminApiRoute($api->addRoute("/provinces/admin.{format:csv|json}"), "provinces");
    $this->addApiRoute($api->addRoute("/provinces/select.json"), "provinces", ProvincesSelectCollection::class);
  }
}
