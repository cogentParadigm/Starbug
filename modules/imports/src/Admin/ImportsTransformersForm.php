<?php
namespace Starbug\Imports\Admin;

use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\DatabaseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Templates\TemplateInterface;
use Starbug\Imports\Transform\Factory;

class ImportsTransformersForm extends FormDisplay {
  public $model = "imports_transformers";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    protected Factory $transformers,
    protected DatabaseInterface $db
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
  }
  public function buildDisplay($options) {
    $data = $this->getPost();
    if ($this->success() && empty($data["id"])) {
      $this->setPost("id", $this->db->getInsertId($this->model));
    }
    $transformers = $this->transformers->getTransformers();
    $this->add([
      "type",
      "input_type" => "select",
      "options" => array_combine(
        array_column($transformers, "name"),
        array_keys($transformers)
      ),
      "data-dojo-type" => "starbug/form/Dependency"
    ]);
    foreach ($transformers as $type => $transformer) {
      if (!empty($transformer["settings"])) {
        foreach ($transformer["settings"] as $setting) {
          $field = [$type."_".$setting["name"]];
          $field["id"] = $field[0];
          $field = array_merge($field, $setting);
          $field["data-dojo-type"] = "starbug/form/Dependent";
          $field["data-dojo-props"] = "values: {'type': ['{$type}']}, clearDisabled: false";
          $field["data-depend"] = "type";
          $this->add($field);
        }
      }
    }
  }
}
