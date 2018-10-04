<?php
namespace Starbug\Db;
use Starbug\Db\Schema\SchemerInterface;
class MigrateCommand {
	function __construct(SchemerInterface $schemer) {
		$this->schemer = $schemer;
	}
	public function run($argv) {
		$this->schemer->migrate();
	}
}
