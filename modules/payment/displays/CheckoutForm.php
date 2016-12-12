<?php
namespace Starbug\Payment;
use Starbug\Core\FormDisplay;
class CheckoutForm extends FormDisplay {
	public $model = "orders";
	public $collection = "OrdersForm";
	public $default_action = "checkout";
	public $submit_label = "Contintue to Payment";
	function build_display($options) {
		$this->layout->add(["a", "left" => "div.col-sm-6", "right" => "div.col-sm-6"]);
		$this->add([
			"shipping_panel_top",
			"pane" => "left",
			"input_type" => "html",
			"value" => '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Shipping Address</h3></div><div class="panel-body">'
		]);
		$this->add(["shipping_address", "pane" => "left", "input_type" => "address", "nolabel" => ""]);
		$this->add([
			"shipping_panel_bottom",
			"pane" => "left",
			"input_type" => "html",
			"value" => '</div></div>'
		]);
		$this->add([
			"billing_panel_top",
			"pane" => "right",
			"input_type" => "html",
			"value" => '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Billing Address</h3></div><div class="panel-body">'
		]);
		$this->add(["billing_address", "pane" => "right", "input_type" => "address", "nolabel" => ""]);
		$this->add([
			"billing_panel_bottom",
			"pane" => "right",
			"input_type" => "html",
			"value" => '</div></div>'
		]);
		$this->actions->add([$this->default_action, "class" => "btn-primary"]);
	}
}
