<?php
namespace Starbug\Notifications\Admin;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
  "route.providers" => add([
    get(RouteProvider::class)
  ]),
  "Starbug\\Notifications\\Admin\\Display\\NotificationsForm" => autowire()
    ->method("setNotificationManager", get("Starbug\Notifications\NotificationManagerInterface"))
];
