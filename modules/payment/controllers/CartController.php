<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;

class CartController extends Controller {
  public function __construct(Cart $cart) {
    $this->cart = $cart;
  }
  public function init() {
    $this->assign("model", "orders");
  }
  public function default_action() {
    if (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("cart/default.html");
    }
  }
  public function add() {
    $product = $this->cart->addProduct($this->request->getParameters());
    if ($this->request->hasParameter("to")) {
      $this->redirect($this->request->getParameter("to"));
    } else {
      $product['description'] = '<strong>'.$product['description'].'</strong>';
      $this->assign("product", $product);
      $this->render("cart/add.html");
    }
  }
}
