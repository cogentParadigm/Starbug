<?php
namespace Starbug\Intl;

use DI;
use Starbug\Core\Routing\RoutesHelper;

return [
  'routes' => DI\add(
    [
      "address" => ["controller" => "address"],
      "address/create" => ["controller" => "address", "action" => "create"],
      "address/update/{id:[0-9]+}" => ["controller" => "address", "action" => "update"],
      "address/form[/{locale}]" => ["controller" => "address", "action" => "form"]
    ]
    + RoutesHelper::crudiRoutes("admin/countries", "Starbug\Intl\AdminCountriesController")
    + RoutesHelper::crudiRoutes("admin/provinces", "Starbug\Intl\AdminProvincesController")
  ),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Intl\Migration')
  ])
];
