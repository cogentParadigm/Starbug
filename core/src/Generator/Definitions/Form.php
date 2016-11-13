<?php
namespace Starbug\Core\Generator\Definitions;
use Starbug\Core\Generator\Definition;
use Starbug\Db\Schema\SchemerInterface;
class Form extends Definition {
	public function __construct(SchemerInterface $schemer) {
		$this->schemer = $schemer;
	}
	public function build($options = []) {
		parent::build($options);
		$schema = $this->schemer->getSchema();
		$table = $schema->getTable($options["model"]);
		foreach ($table->getOptions() as $key => $value) {
			$this->setParameter($key, $value);
		}
		$this->setParameter("fields", $table->getColumns());
		$this->addTemplate(
			"generate/form/form",
				$this->module."/displays/".ucwords($options["model"])."Form.php"
		);
	}
}
?>
