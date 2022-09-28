<?php
namespace Starbug\Emails;

use DI;
use Starbug\Core\Mailer;
use Starbug\Core\MailerInterface;

return [
  "emails.whitelist.enabled" => false,
  "emails.whitelist" => [],
  "route.providers" => DI\add([
    DI\get(RouteProvider::class)
  ]),
  "db.schema.migrations" => DI\add([
    DI\get(Migration::class)
  ]),
  MailerInterface::class => DI\autowire(Mailer::class)
    ->constructorParameter("whitelistEnabled", DI\get("emails.whitelist.enabled"))
    ->constructorParameter("whitelist", DI\get("emails.whitelist"))
];
