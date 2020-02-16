<?php
namespace Starbug\Content;

use DI;
use Starbug\Core\Routing\RoutesHelper;

return [
  "routes" => DI\add(
    [
      "pages" => ["controller" => "pages"],
      "pages/view/{id:[0-9]+}" => ["controller" => "pages", "action" => "view"]
    ]
    + RoutesHelper::crudiRoutes("admin/categories", "Starbug\Content\AdminCategoriesController")
    + RoutesHelper::crudiRoutes("admin/pages", "Starbug\Content\AdminPagesController")
  ),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Content\Migration')
  ]),
  'Starbug\Core\Routing\RouterInterface' => DI\object()
    ->method('addAliasStorage', DI\get('Starbug\Content\AliasStorage')),
];
