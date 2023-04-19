<?php
namespace Starbug\Emails;

use function DI\add;
use function DI\get;
use function DI\autowire;
use DI;
use Starbug\Core\Mailer;
use Starbug\Core\MailerInterface;

return [
  "emails.whitelist.enabled" => false,
  "emails.whitelist" => [],
  "route.providers" => add([
    get(RouteProvider::class)
  ]),
  "db.schema.migrations" => add([
    get(Migration::class)
  ]),
  MailerInterface::class => autowire(Mailer::class)
    ->constructorParameter("whitelistEnabled", get("emails.whitelist.enabled"))
    ->constructorParameter("whitelist", get("emails.whitelist"))
];
