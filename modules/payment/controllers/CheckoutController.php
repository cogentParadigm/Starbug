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
  public function init() {
    $this->assign("model", "orders");
    $this->assign("cart", $this->cart);
  }
  public function defaultAction() {
    if ($this->db->success("orders", "checkout")) {
      $this->redirect("checkout/payment");
    } elseif (empty($this->cart)) {
      $this->render("cart/empty.html");
    } elseif ($this->user->loggedIn()) {
      $this->render("checkout/default.html");
    } else {
      $this->render("checkout/login.html");
    }
  }
  public function guest() {
    if ($this->db->success("orders", "checkout")) {
      $this->redirect("checkout/payment");
    } elseif ($this->user->loggedIn()) {
      $this->redirect("checkout");
    } elseif (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/default.html");
    }
  }
  public function payment() {
    if ($this->db->success("orders", "payment")) {
      $this->redirect("checkout/success/".$this->request->getPost('orders', 'id'));
    } elseif (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/payment.html");
    }
  }
  public function success($id) {
    $this->assign("id", $id);
    $this->render("checkout/success.html");
  }
}
