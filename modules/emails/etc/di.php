<?php
namespace Starbug\Emails;

use DI;
use Starbug\Core\Routing\RoutesHelper;

return [
  "routes" => DI\add(
    RoutesHelper::crudiRoutes("admin/emails", "Starbug\Emails\AdminEmailsController")
  ),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Emails\Migration')
  ])
];
