<?php
namespace Starbug\Db;

use function DI\autowire;
use function DI\get;
use function DI\add;
use DI;
use Psr\Container\ContainerInterface;
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
    "addslashes" => "Starbug\Core\StoreAddslashesHook",
    "alias" => "Starbug\Core\StoreAliasHook",
    "category" => "Starbug\Core\StoreCategoryHook",
    "confirm" => "Starbug\Core\StoreConfirmHook",
    "date" => "Starbug\Core\StoreDateHook",
    "datetime" => "Starbug\Core\StoreDatetimeHook",
    "default" => "Starbug\Core\StoreDefaultHook",
    "email" => "Starbug\Core\StoreEmailHook",
    "exclude" => "Starbug\Core\StoreExcludeHook",
    "filter_var" => "Starbug\Core\StoreFilterVarHook",
    "groups" => "Starbug\Core\StoreGroupsHook",
    "length" => "Starbug\Core\StoreLengthHook",
    "materialized_path" => "Starbug\Core\StoreMaterializedPathHook",
    "md5" => "Starbug\Core\StoreMd5Hook",
    "operation" => "Starbug\Core\StoreOperationHook",
    "optional_update" => "Starbug\Core\StoreOptionalUpdateHook",
    "ordered" => "Starbug\Core\StoreOrderedHook",
    "owner" => "Starbug\Core\StoreOwnerHook",
    "password" => "Starbug\Core\StorePasswordHook",
    "references" => "Starbug\Core\StoreReferencesHook",
    "required" => "Starbug\Core\StoreRequiredHook",
    "selection" => "Starbug\Core\StoreSelectionHook",
    "slug" => "Starbug\Core\StoreSlugHook",
    "terms" => "Starbug\Core\StoreTermsHook",
    "time" => "Starbug\Core\StoreTimeHook",
    "timestamp" => "Starbug\Core\StoreTimestampHook",
    "type" => "Starbug\Core\StoreTypeHook",
    "unique" => "Starbug\Core\StoreUniqueHook",
    "upload" => "Starbug\Core\StoreUploadHook"
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
