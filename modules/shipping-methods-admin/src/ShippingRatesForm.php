<?php
namespace Starbug\ShippingMethods\Admin;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Templates\TemplateInterface;

class ShippingRatesForm extends FormDisplay {
  public $model = "shipping_rates";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    protected DatabaseInterface $db
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
  }
  public function buildDisplay($options) {
    if ($this->success() && !$this->hasPost("id")) {
      $this->setPost("id", $this->db->getInsertId($this->model));
    }
    // $this->add(["additive", "info" => "Check to make this an add-on rather than the base rate."]);
    $this->add(["name"]);
    $this->add(["price", "info" => "Enter price in cents. For example, enter 5000 for $50."]);
    $this->add(["product_types", "input_type" => "multiple_select", "from" => "product_types", "query" => "Select"]);
    $this->add(["product_options", "input_type" => "text", "data-dojo-type" => "sb/form/CRUDList", "data-dojo-props" => "model:'shipping_rates_product_options', newItemLabel:'Add Product Option Condition'"]);
  }
}
