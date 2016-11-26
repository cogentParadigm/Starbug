<?php
namespace Starbug\Payment;
use Starbug\Core\FormDisplay;
class OrdersForm extends FormDisplay {
	public $model = "orders";
	public $cancel_url = "admin/orders";
	public $collection = "OrdersForm";
	function build_display($options) {
		$this->layout->add(["top", "top" => "div.col-xs-12"]);
		$this->layout->add(["bottom", "left" => "div.col-sm-6", "right" => "div.col-sm-6"]);
		$this->add(["email", "pane" => "top"]);
		$this->add(["phone", "pane" => "top"]);
		$this->add(["order_status", "pane" => "top", "input_type" => "select", "options" => ["Cart", "Pending", "Processing", "Completed"], "values" => ["cart", "pending", "processing", "completed"]]);
		$this->add(["billing_address", "pane" => "left", "input_type" => "address"]);
		$this->add(["shipping_address", "pane" => "right", "input_type" => "address"]);
	}
}
