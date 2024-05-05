<?php
namespace Starbug\Products\Admin\ProductOptions;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Templates\TemplateInterface;

class ProductOptionsForm extends FormDisplay {
  public $model = "product_options";
  protected $tableSchema;
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    SchemerInterface $schemer,
    protected DatabaseInterface $db
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
    $this->tableSchema = $schemer->getSchema();
  }
  public function buildDisplay($options) {
    $tree = $this->getOptionsTree($options["product_types_id"] ?? $this->get("product_types_id"));
    $this->add("name");
    $this->add("slug");
    $this->add(["description", "input_type" => "textarea"]);
    $this->add(["type", "input_type" => "select", "options" => "Fieldset,Text,Textarea,Checkbox,Select List,Value,File,Reference,Hidden", "data-dojo-type" => "starbug/form/Dependency", "data-dojo-props" => "key:'type'"]);
    $this->add(["reference_type", "input_type" => "select", "options" => $this->getReferenceTypes(), "data-dojo-type" => "starbug/form/Dependent", "data-dojo-props" => "key:'type',values:['Reference']"]);
    $this->add(["required", "input_type" => "checkbox", "value" => 1]);
    $this->add(["parent", "input_type" => "select"] + $tree);
    $this->add("position");
    $this->add(["columns", "input_type" => "select", "options" => "1,2,3,4,5,6,7,8,9,10,11,12"]);
  }
  public function getOptionsTree($type, $parent = 0, $prefix = "") {
    $options = $values = [""];
    $items = $this->db->query("product_options")->conditions(["product_types_id" => $type, "parent" => $parent])->all();
    foreach ($items as $item) {
      if (!empty($prefix)) {
        $item['name'] = $prefix.$item['name'];
      }
      $values[] = $item['id'];
      $options[] = $item['name'];
      $results = $this->getOptionsTree($type, $item['id'], $item['name'].': ');
      $values = array_merge($values, $results['values']);
      $options = array_merge($options, $results['options']);
    }
    return ['options' => $options, 'values' => $values];
  }
  public function getReferenceTypes() {
    return array_merge([""], array_keys($this->tableSchema->getTables()));
  }
}
