<?php
namespace Starbug\Payment;
use Starbug\Core\Controller;
use Starbug\Core\IdentityInterface;
use Starbug\Core\DatabaseInterface;
class CheckoutController extends Controller {
	public function __construct(Cart $cart, IdentityInterface $user, DatabaseInterface $db) {
		$this->cart = $cart;
		$this->user = $user;
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "orders");
		$this->assign("cart", $this->cart);
	}
	function default_action() {
		if (empty($this->cart)) {
			$this->render("cart/empty");
		} else if ($this->user->loggedIn()) {
			$this->render("checkout/default");
		} else {
			$this->render("checkout/login");
		}
	}
	function guest() {
		if ($this->user->loggedIn()) {
			$this->redirect("checkout");
			return;
		}
		if (empty($this->cart)) {
			$this->render("cart/empty");
		} else {
			$this->render("checkout/default");
		}
	}
	function payment() {
		if ($this->db->success("orders", "payment")) {
			$this->redirect("checkout/success/".$this->request->getPost('orders', 'id'));
		} else if (empty($this->cart)) {
			$this->render("cart/empty");
		} else {
			$this->render("checkout/payment");
		}
	}
}
?>
