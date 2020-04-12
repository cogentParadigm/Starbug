<?php
use Psr\Container\ContainerInterface;
use Starbug\Db\Schema\QueryCompilerHook;

return [
  'Starbug\Db\Schema\*Interface' => DI\autowire('Starbug\Db\Schema\*'),
  'Starbug\Db\Query\*Interface' => DI\autowire('Starbug\Db\Query\*'),
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
  'Starbug\Db\Query\CompilerInterface' => DI\autowire('Starbug\Db\Query\Compiler')->method('addHooks', DI\get('db.query.compiler.hooks')),
  'db.query.compiler.hooks' => [
    DI\get('Starbug\Db\Schema\QueryCompilerHook')
  ],
  'db.query.builder.extensions' => [
    'search' => DI\get('Starbug\Db\Query\Extensions\Search'),
    'action' => DI\get('Starbug\Db\Query\Extensions\Action')
  ]
];
