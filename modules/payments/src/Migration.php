<?php
namespace Starbug\Payments;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    // Payment gateways and gateway settings.
    $this->schema->addTable(["payment_gateways", "label_select" => "payment_gateways.name", "groups" => false],
      ["name", "type" => "string", "length" => "255"],
      ["description", "type" => "text", "default" => ""],
      ["is_active", "type" => "bool", "default" => "0"],
      ["is_test_mode", "type" => "bool", "default" => "0"]
    );
    $this->schema->addTable(["payment_gateway_settings", "label_select" => "payment_gateway_settings.name", "groups" => false],
      ["payment_gateway_id", "type" => "int", "references" => "payment_gateways id", "alias" => "%name%"],
      ["name", "type" => "string", "length" => "256"],
      ["type", "type" => "string", "input_type" => "select", "options" => "text,textarea,select,checkbox,radio,password"],
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
    $this->schema->addTable(["payments", "groups" => false],
      ["amount", "type" => "int", "default" => "0"],
      ["response_code", "type" => "int"],
      ["txn_id", "type" => "string", "length" => "32", "default" => ""],
      ["response", "type" => "text"]
    );
  }
}
