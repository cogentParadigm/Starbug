<?php
namespace Starbug\Payment;

use Starbug\Db\DatabaseInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Controller;

class CheckoutController extends Controller {
  public function __construct(Cart $cart, SessionHandlerInterface $session, DatabaseInterface $db) {
    $this->cart = $cart;
    $this->session = $session;
    $this->db = $db;
  }
  public function defaultAction() {
    if (count($this->cart) == 0) {
      return $this->render("cart/empty.html");
    } elseif ($this->session->loggedIn()) {
      return $this->render("checkout/default.html");
    } else {
      return $this->render("checkout/login.html");
    }
  }
  public function guest() {
    if ($this->session->loggedIn()) {
      $this->response->redirect("checkout");
    } elseif (count($this->cart) == 0) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/default.html");
    }
  }
  public function payment() {
    $this->assign("cart", $this->cart);
    if (count($this->cart) == 0) {
      return $this->render("cart/empty.html");
    } else {
      return $this->render("checkout/payment.html");
    }
  }
  public function success($id) {
    $this->assign("id", $id);
    return $this->render("checkout/success.html");
  }
}
