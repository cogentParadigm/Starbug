<?php
namespace Starbug\ShippingMethods;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(["shipping_rates", "label_select" => "CONCAT(shipping_rates.name, ' - $', ROUND(shipping_rates.price/100, 2), IF(additive=1, ' (additive)', ''))"],
      ["name", "type" => "string", "length" => "128"],
      ["additive", "type" => "bool", "default" => "0"],
      ["product_types", "type" => "product_types"],
      ["product_options", "type" => "product_options"],
      ["price", "type" => "int", "default" => "0"]
    );
    $this->schema->addTable(["shipping_rates_product_options", "label_select" => "CONCAT(shipping_rates_product_options.product_options_id.name, ' ', shipping_rates_product_options.operator, ' ', shipping_rates_product_options.value)"],
      ["operator", "type" => "string", "default" => "="],
      ["value", "type" => "text", "default" => ""]
    );
    $this->schema->addTable(["shipping_methods", "label_select" => "shipping_methods.name"],
      ["name", "type" => "string", "length" => "128"],
      ["description", "type" => "text", "default" => ""],
      ["shipping_rates", "type" => "shipping_rates", "table" => "shipping_rates"],
      ["position", "type" => "int", "ordered" => ""]
    );
  }
}
