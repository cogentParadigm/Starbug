<?php
namespace Starbug\Payment;
use Starbug\Core\FormDisplay;
class PaymentForm extends FormDisplay {
	public $model = "orders";
	public $collection = "OrdersForm";
	public $default_action = "payment";
	public $submit_label = "Place Order";
	function build_display($options) {
		$this->layout->add(["a", "top" => "div", "middle" => "div", "bottom" => "div"]);
		$this->add(["email", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"]);
		$this->add(["phone", "label" => "Phone Number", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"]);
		$this->add(["card_number", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"]);
		$this->add(["card_holder", "label" => "Name on card", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"]);
		$this->add(["expiration_date[month]", "pane" => "middle", "div" => "col-xs-6", "label" => "Expiration<br/>Month", "input_type" => "select", "range" => "1-12"]);
		$this->add(["expiration_date[year]", "pane" => "middle", "div" => "col-xs-6", "label" => "Expiration<br/>Year", "before" => "/", "input_type" => "select", "range" => date("Y")."-".(intval(date("Y"))+20)]);
		$this->add(["cvv", "label" => "Security code", "pane" => "bottom", "div" => "col-xs-12", "input_type" => "text"]);
		$this->actions->add([$this->default_action, "class" => "btn-primary"]);
	}
}
?>
