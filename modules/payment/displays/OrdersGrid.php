<?php
namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class OrdersGrid extends GridDisplay {
  public $model = "orders";
  public $action = "admin";
  public function build_display($options) {
    $this->add(["id", "label" => "ID", "readonly" => ""]);
    $this->add(["customer", "readonly" => ""]);
    $this->add(["total_formatted", "label" => "Total", "readonly" => ""]);
    $this->add(["order_status", "label" => "Status", "readonly" => ""]);
    $this->add(["created", "readonly" => ""]);
    $this->add(["purchased", "readonly" => ""]);
    $this->add(["row_options", "plugin" => "payment.grid.columns.orders"]);
  }
}
