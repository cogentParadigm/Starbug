<?php
namespace Starbug\Core\Generator\Definitions;
use Starbug\Core\Generator\Definition;
use Starbug\Db\Schema\SchemerInterface;
class Grid extends Definition {
	public function __construct(SchemerInterface $schemer) {
		$this->schemer = $schemer;
	}
	public function build(array $options = []) {
		parent::build($options);
		$className = str_replace(" ", "", ucwords(str_replace("_", " ", $options["model"])));
		$schema = $this->schemer->getSchema();
		$table = $schema->getTable($options["model"]);
		foreach ($table->getOptions() as $key => $value) {
			$this->setParameter($key, $value);
		}
		$this->setParameter("fields", $table->getColumns());
		$this->setParameter("className", $className);
		$this->addTemplate(
			"generate/grid/grid.php",
			$this->module."/displays/".$className."Grid.php"
		);
	}
}
