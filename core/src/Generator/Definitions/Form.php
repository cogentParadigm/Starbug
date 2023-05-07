<?php
namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\Definition;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Modules\Configuration;

class Form extends Definition {
  public function __construct(Configuration $modules, SchemerInterface $schemer) {
    parent::__construct($modules);
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
    $this->setParameter("fields", $this->getFields($table->getColumns()));
    $this->setParameter("className", $className);
    $this->addDirectory($this->getSourcePath($options));
    $this->addTemplate(
      "generate/form/form.php",
      $this->getSourcePath($options)."/".$className."Form.php"
    );
  }
  protected function getSourcePath($options) {
    return implode("/", array_filter([$this->module["path"]."/src", $options["dir"] ?? ""]));
  }
  protected function getFields($columns) {
    return array_filter($columns, function ($key) {
      return !in_array($key, ["id", "owner", "created", "modified", "deleted"]);
    }, ARRAY_FILTER_USE_KEY);
  }
}
