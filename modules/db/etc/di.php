<?php
use Interop\Container\ContainerInterface;
return [
	'Starbug\Db\Schema\*Interface' => DI\object('Starbug\Db\Schema\*'),
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
	}
];
