<?php
namespace Starbug\Imports\Admin;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Db\Schema\SchemerInterface;

class ImportsFieldOptions {
  protected $schema;
  public function __construct(
    SchemerInterface $schemer,
    protected ServerRequestInterface $request
  ) {
    $this->schema = $schemer->getSchema();
  }
  public function __invoke($options) {
    $model = $this->request->getQueryParams()["model"] ?? $options["model"];
    $columns = $this->schema->getColumn($model);
    $options = array_keys($columns);
    foreach ($columns as $name => $props) {
      if ($props["type"] == "int" && isset($props["references"]) && isset($props["operation"])) {
        $relatedModel = explode(" ", $props["references"])[0];
        $relatedCols = $this->__invoke($relatedModel);
        foreach ($relatedCols as $relatedCol) {
          $options[] = $name . ": " . $relatedCol;
        }
      }
    }
    return $options;
  }
}
