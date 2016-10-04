<?php
namespace Starbug\Payment;
use Starbug\Core\FormCollection;
use Starbug\Core\ModelFactoryInterface;
class OrdersFormCollection extends FormCollection {
	public function __construct(ModelFactoryInterface $models, Cart $cart) {
		$this->models = $models;
		$this->cart = $cart;
	}
	public function build($query, &$ops) {
		if (empty($ops["action"])) $ops["action"] = "checkout";
		if ($ops["action"] == "checkout") {
			if ($ops["id"] !== $this->cart->get("id")) $query->action($ops["action"], "orders");
		} else {
			$query->action($ops["action"], "orders");
		}
		$query->condition("orders.id", $ops["id"]);
		return $query;
	}
}
?>
