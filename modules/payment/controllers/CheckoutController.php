<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\IdentityInterface;
use Starbug\Core\DatabaseInterface;

class CheckoutController extends Controller {
  public $routes = [
    "success" => "success/{id}"
  ];
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
    if ($this->db->success("orders", "checkout")) {
      $this->redirect("checkout/payment");
    } else if (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else if ($this->user->loggedIn()) {
      $this->render("checkout/default.html");
    } else {
      $this->render("checkout/login.html");
    }
  }
  function guest() {
    if ($this->db->success("orders", "checkout")) {
      $this->redirect("checkout/payment");
    } else if ($this->user->loggedIn()) {
      $this->redirect("checkout");
    } else if (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/default.html");
    }
  }
  function payment() {
    if ($this->db->success("orders", "payment")) {
      $this->redirect("checkout/success/".$this->request->getPost('orders', 'id'));
    } else if (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/payment.html");
    }
  }
  function success($id) {
    $this->assign("id", $id);
    $this->render("checkout/success.html");
  }
}
