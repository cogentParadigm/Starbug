<?php
namespace Starbug\Db;

use function DI\autowire;
use function DI\get;
use function DI\add;
use DI;
use Psr\Container\ContainerInterface;
use Starbug\Db\Helper\SchemaHelper;
use Starbug\Db\Query\Hook\StoreAddslashesHook;
use Starbug\Db\Query\Hook\StoreAliasHook;
use Starbug\Db\Query\Hook\StoreConfirmHook;
use Starbug\Db\Query\Hook\StoreDateHook;
use Starbug\Db\Query\Hook\StoreDatetimeHook;
use Starbug\Db\Query\Hook\StoreDefaultHook;
use Starbug\Db\Query\Hook\StoreEmailHook;
use Starbug\Db\Query\Hook\StoreExcludeHook;
use Starbug\Db\Query\Hook\StoreFilterVarHook;
use Starbug\Db\Query\Hook\StoreGroupsHook;
use Starbug\Db\Query\Hook\StoreLengthHook;
use Starbug\Db\Query\Hook\StoreMaterializedPathHook;
use Starbug\Db\Query\Hook\StoreMd5Hook;
use Starbug\Db\Query\Hook\StoreOperationHook;
use Starbug\Db\Query\Hook\StoreOptionalUpdateHook;
use Starbug\Db\Query\Hook\StoreOrderedHook;
use Starbug\Db\Query\Hook\StoreOwnerHook;
use Starbug\Db\Query\Hook\StorePasswordHook;
use Starbug\Db\Query\Hook\StoreReferencesHook;
use Starbug\Db\Query\Hook\StoreRequiredHook;
use Starbug\Db\Query\Hook\StoreSelectionHook;
use Starbug\Db\Query\Hook\StoreSlugHook;
use Starbug\Db\Query\Hook\StoreTimeHook;
use Starbug\Db\Query\Hook\StoreTimestampHook;
use Starbug\Db\Query\Hook\StoreTypeHook;
use Starbug\Db\Query\Hook\StoreUniqueHook;
use Starbug\Db\Query\Hook\StoreUploadHook;
use Starbug\Db\Schema\QueryCompilerHook;
use Starbug\Db\Script\Describe;
use Starbug\Db\Script\Migrate;
use Starbug\Db\Script\Query;
use Starbug\Db\Script\QueryCollection;
use Starbug\Db\Script\Remove;
use Starbug\Db\Script\Store;

return [
  "db" => "default",
  "Starbug\Db\*Interface" => autowire("Starbug\Db\*"),
  'Starbug\Db\Schema\*Interface' => autowire('Starbug\Db\Schema\*'),
  'Starbug\Db\Query\*Interface' => autowire('Starbug\Db\Query\*'),
  Database::class => autowire()
    ->method('setTimeZone', get('time_zone'))
    ->method('setDatabase', get("databases.active")),
  'Starbug\Db\Schema\SchemaInterface' => function (ContainerInterface $c) {
    $schema = $c->get('Starbug\Db\Schema\Schema');
    $hooks = $c->get('db.schema.hooks');
    foreach ($hooks as $hook) {
      $schema->addHook($hook);
    }
    return $schema;
  },
  'Starbug\Db\Schema\SchemerInterface' => function (ContainerInterface $c) {
    $schemer = $c->get('Starbug\Db\Schema\Schemer');
    $c->set('Starbug\Db\Schema\SchemerInterface', $schemer);
    $migrations = $c->get('db.schema.migrations');
    foreach ($migrations as $migration) {
      $schemer->addMigration($migration);
    }
    return $schemer;
  },
  'Starbug\Db\Schema\QueryCompilerHook' => function (ContainerInterface $c) {
    $schemer = $c->get('Starbug\Db\Schema\SchemerInterface');
    return new QueryCompilerHook($schemer->getSchema());
  },
  'Starbug\Db\Query\CompilerInterface' => autowire('Starbug\Db\Query\Compiler')->method('addHooks', get('db.query.compiler.hooks')),
  "Starbug\Db\Query\ExecutorHookFactoryInterface" => autowire("Starbug\Db\Query\ExecutorHookFactory")
    ->constructorParameter("hooks", get("db.query.executor.hooks")),
  'db.query.compiler.hooks' => [
    get('Starbug\Db\Schema\QueryCompilerHook')
  ],
  'db.query.builder.extensions' => [
    'search' => get('Starbug\Db\Query\Extensions\Search'),
    'getSearchFields' => get('Starbug\Db\Query\Extensions\Search'),
    'action' => get('Starbug\Db\Query\Extensions\Action')
  ],
  "db.query.executor.hooks" => [
    "addslashes" => StoreAddslashesHook::class,
    "alias" => StoreAliasHook::class,
    "confirm" => StoreConfirmHook::class,
    "date" => StoreDateHook::class,
    "datetime" => StoreDatetimeHook::class,
    "default" => StoreDefaultHook::class,
    "email" => StoreEmailHook::class,
    "exclude" => StoreExcludeHook::class,
    "filter_var" => StoreFilterVarHook::class,
    "user_groups" => StoreGroupsHook::class,
    "length" => StoreLengthHook::class,
    "materialized_path" => StoreMaterializedPathHook::class,
    "md5" => StoreMd5Hook::class,
    "operation" => StoreOperationHook::class,
    "optional_update" => StoreOptionalUpdateHook::class,
    "ordered" => StoreOrderedHook::class,
    "owner" => StoreOwnerHook::class,
    "password" => StorePasswordHook::class,
    "references" => StoreReferencesHook::class,
    "required" => StoreRequiredHook::class,
    "selection" => StoreSelectionHook::class,
    "slug" => StoreSlugHook::class,
    "time" => StoreTimeHook::class,
    "timestamp" => StoreTimestampHook::class,
    "type" => StoreTypeHook::class,
    "unique" => StoreUniqueHook::class,
    "upload" => StoreUploadHook::class
  ],
  "databases.active" => function (ContainerInterface $container) {
    return $container->get("databases.".$container->get("db"));
  },
  "template.helpers" => add([
    "schema" => SchemaHelper::class
  ]),
  "scripts.describe" => Describe::class,
  "scripts.migrate" => Migrate::class,
  "scripts.query" => Query::class,
  "scripts.query-collection" => QueryCollection::class,
  "scripts.remove" => Remove::class,
  "scripts.store" => Store::class
];
