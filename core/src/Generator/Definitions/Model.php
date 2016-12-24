<?php
namespace Starbug\Core\Generator\Definitions;
use Starbug\Core\Generator\Definition;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Core\ConfigInterface;
class Model extends Definition {
	public function __construct(SchemerInterface $schemer, ConfigInterface $config) {
		$this->schemer = $schemer;
		$this->config = $config;
	}
	public function build($options = []) {
		parent::build($options);
		$className = str_replace(" ", "", ucwords(str_replace("_", " ", $options["model"])));
		$schema = $this->schemer->getSchema();
		$table = $schema->getTable($options["model"]);
		foreach ($table->getOptions() as $key => $value) {
			$this->setParameter($key, $value);
		}
		$this->setParameter("fields", $table->getColumns());
		$factory = $this->config->get("models", "factory");
	  $factory = isset($factory[$options["model"]]) ? $factory[$options["model"]] : array();
		$use = [];
		foreach ($factory as $n => $t) {
			if (false != strpos($t, "\\")) {
				$use[] = "use ".$t.";";
				$parts = explode("\\", $t);
				$factory[$n] = end($parts);
			}
		}
		$this->setParameter("factory", $factory);
		$this->setParameter("use", $use);
		$this->setParameter("className", $className);
		$this->addTemplate(
			"generate/model/base",
				"var/models/".$className."Model.php"
		);
		if (empty($options["update"])) {
			$this->addTemplate(
				"generate/model/model",
					$this->module."/models/".$className.".php"
			);
		}
	}
}
