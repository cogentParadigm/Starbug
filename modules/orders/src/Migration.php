<?php
namespace Starbug\Orders;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(["lines", "groups" => false],
      ["type", "type" => "string"],
      ["description", "type" => "string", "length" => "255"],
      ["price", "type" => "int", "default" => "0"],
      ["qty", "type" => "int", "default" => "1"]
    );
    $this->schema->addTable(["product_lines", "base" => "lines", "groups" => false],
      ["product", "type" => "int", "references" => "products id"],
      ["options", "type" => "product_options"]
    );
    $this->schema->addTable(["product_lines_options"],
      ["options_id", "type" => "int", "references" => "product_options id", "update" => "cascade", "delete" => "cascade", "alias" => "%slug%"],
      ["value", "type" => "string", "length" => "255", "default" => ""]
    );
    $this->schema->addTable(["shipping_lines", "base" => "lines"],
      ["method", "type" => "int", "references" => "shipping_methods id"]
    );
    $this->schema->addTable(["tax_lines", "base" => "lines", "groups" => false]);
    $this->schema->addTable(
      [
        "orders",
        "search" => "orders.id,orders.order_status,orders.email,orders.phone,".
                    "orders.billing_address.recipient,orders.shipping_address.recipient"
      ],
      ["subtotal", "type" => "string", "length" => "32", "default" => ""],
      ["total", "type" => "string", "length" => "32"],
      ["order_status", "type" => "string", "length" => "128", "default" => "cart"],
      ["lines", "type" => "lines", "table" => "lines", "optional" => ""],
      ["token", "type" => "string", "length" => "128", "default" => ""],
      ["billing_address", "type" => "int", "references" => "address id", "null" => "", "operation" => "create"],
      ["shipping_address", "type" => "int", "references" => "address id", "null" => "", "operation" => "create"],
      ["billing_same", "type" => "bool", "default" => "0"],
      ["email", "type" => "string", "length" => "128"],
      ["phone", "type" => "string"],
      ["payments", "type" => "payments", "table" => "payments"]
    );

    $this->schema->addTable("address",
      ["order_token", "type" => "string", "default" => ""]
    );
  }
}
