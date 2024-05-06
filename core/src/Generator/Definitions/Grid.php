<?php
namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\Definition;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Modules\Configuration;

class Grid extends Definition {
  public function __construct(
    Configuration $modules,
    protected SchemerInterface $schemer
  ) {
    parent::__construct($modules);
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
    $this->addDirectory($this->getSourcePath($options));
    $this->addTemplate(
      "generate/grid/grid.php",
      $this->getSourcePath($options)."/".$className."Grid.php"
    );
  }
  protected function getSourcePath($options) {
    return implode("/", array_filter([$this->module["path"]."/src", $options["dir"] ?? ""]));
  }
}
