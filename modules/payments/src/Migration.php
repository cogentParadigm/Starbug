<?php
namespace Starbug\Payments;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    // Payment gateways and gateway settings.
    $this->schema->addTable(["payment_gateways", "label_select" => "payment_gateways.name"],
      ["name", "type" => "string", "length" => "255"],
      ["description", "type" => "text", "default" => ""],
      ["is_active", "type" => "bool", "default" => "0"],
      ["is_test_mode", "type" => "bool", "default" => "0"]
    );
    $this->schema->addTable(["payment_gateway_settings", "label_select" => "payment_gateway_settings.name"],
      ["payment_gateway_id", "type" => "int", "references" => "payment_gateways id", "alias" => "%name%"],
      ["name", "type" => "string", "length" => "256"],
      ["type", "type" => "string", "default" => "text"],
      ["options", "type" => "text", "default" => ""],
      ["test_mode_value", "type" => "text", "default" => ""],
      ["live_mode_value", "type" => "text", "default" => ""],
      ["description", "type" => "text", "default" => ""]
    );
    // store payment gateways
    $this->schema->addRow("payment_gateways",
      ["name" => "Authorize.Net", "description" => "Purchase with credit card using Authorize.net"],
      ["is_test_mode" => "1", "is_active" => "1"]
    );
    $this->schema->addTable(["payment_cards"],
      ["customer_reference", "type" => "string", "length" => "128", "default" => ""],
      ["card_reference", "type" => "string", "length" => "128"],
      ["brand", "type" => "string"],
      ["number", "type" => "string"],
      ["month", "type" => "int"],
      ["year", "type" => "int"]
    );
    $this->schema->addTable(["payments"],
      ["amount", "type" => "int", "default" => "0"],
      ["response_code", "type" => "int"],
      ["txn_id", "type" => "string", "length" => "32", "default" => ""],
      ["card", "type" => "int", "references" => "payment_cards id", "null" => true, "default" => "NULL"],
      ["response", "type" => "text"]
    );
  }
}
