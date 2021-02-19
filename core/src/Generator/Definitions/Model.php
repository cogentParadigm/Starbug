<?php
namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\Definition;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Core\ConfigInterface;
use Starbug\Modules\Configuration;

class Model extends Definition {
  public function __construct(Configuration $modules, SchemerInterface $schemer, ConfigInterface $config) {
    parent::__construct($modules);
    $this->schemer = $schemer;
    $this->config = $config;
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
    $factory = $this->config->get("models", "factory");
    $factory = isset($factory[$options["model"]]) ? $factory[$options["model"]] : [];
    $use = [];
    foreach ($factory as $n => $t) {
      if (false != strpos($t, "\\")) {
        $use[] = "use ".$t.";";
        if (false != strpos($t, " as ")) {
          $parts = explode(" as ", $t);
        } else {
          $parts = explode("\\", $t);
        }
        $factory[$n] = end($parts);
      }
    }
    $this->setParameter("factory", $factory);
    $this->setParameter("useLines", $use);
    $this->setParameter("className", $className);
    $this->addTemplate(
      "generate/model/base.php",
      "var/models/".$className."Model.php"
    );
    if (empty($options["update"])) {
      $this->addTemplate(
        "generate/model/model.php",
        $this->module["path"]."/models/".$className.".php"
      );
    }
  }
}
