<?php
namespace Starbug\Payment;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class CheckoutController extends Controller {
  public function __construct(Cart $cart, SessionHandlerInterface $session, DatabaseInterface $db) {
    $this->cart = $cart;
    $this->session = $session;
    $this->db = $db;
  }
  public function defaultAction() {
    if ($this->db->success("orders", "checkout")) {
      return $this->redirect("checkout/payment");
    } elseif (empty($this->cart)) {
      return $this->render("cart/empty.html");
    } elseif ($this->session->loggedIn()) {
      return $this->render("checkout/default.html");
    } else {
      return $this->render("checkout/login.html");
    }
  }
  public function guest() {
    if ($this->db->success("orders", "checkout")) {
      $this->response->redirect("checkout/payment");
    } elseif ($this->session->loggedIn()) {
      $this->response->redirect("checkout");
    } elseif (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("checkout/default.html");
    }
  }
  public function payment() {
    $this->assign("cart", $this->cart);
    if ($this->db->success("orders", "payment")) {
      $id = $this->request->getParsedBody()["orders"]["id"];
      return $this->redirect("checkout/success/".$id);
    } elseif (empty($this->cart)) {
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
