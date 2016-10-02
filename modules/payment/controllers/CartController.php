<?php
namespace Starbug\Payment;
use Starbug\Core\Controller;
class CartController extends Controller {
	public function __construct(Cart $cart) {
		$this->cart = $cart;
	}
	function init() {
		$this->assign("model", "orders");
	}
	function default_action() {
		if (empty(count($this->cart))) {
			$this->render("cart/empty");
		} else {
			$this->render("cart/default");
		}
	}
	function add() {
		$this->cart->init();
		$product = $this->cart->addProduct($this->request->getParameters());
		$product['description'] = '<strong>'.$product['description'].'</strong>';
		$this->assign("product", $product);
		$this->render("cart/add");
	}
}
?>
