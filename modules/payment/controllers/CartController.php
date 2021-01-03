<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\InputFilterInterface;

class CartController extends Controller {
  public function __construct(Cart $cart, InputFilterInterface $filter) {
    $this->cart = $cart;
    $this->filter = $filter;
  }
  public function init() {
    $this->assign("model", "orders");
  }
  public function defaultAction() {
    if (empty($this->cart)) {
      $this->render("cart/empty.html");
    } else {
      $this->render("cart/default.html");
    }
  }
  public function add() {
    $queryParams = $this->request->getQueryParams();
    $product = $this->cart->addProduct($queryParams);
    if (!empty($queryParams["to"])) {
      $this->response->redirect($this->filter->normalize($queryParams["to"]));
    } else {
      $product['description'] = '<strong>'.$product['description'].'</strong>';
      $this->assign("product", $product);
      $this->render("cart/add.html");
    }
  }
}
