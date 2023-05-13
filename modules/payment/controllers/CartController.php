<?php
namespace Starbug\Payment;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Routing\Controller;
use Starbug\Core\InputFilterInterface;

class CartController extends Controller {
  public function __construct(Cart $cart, InputFilterInterface $filter, ServerRequestInterface $request) {
    $this->cart = $cart;
    $this->filter = $filter;
    $this->request = $request;
  }
  public function init() {
    $this->assign("model", "orders");
  }
  public function defaultAction() {
    if (empty($this->cart)) {
      return $this->render("cart/empty.html");
    } else {
      return $this->render("cart/default.html");
    }
  }
  public function add() {
    $queryParams = $this->request->getQueryParams();
    $product = $this->cart->addProduct($queryParams);
    if (!empty($queryParams["to"])) {
      return $this->redirect($this->filter->normalize($queryParams["to"]));
    } else {
      $product['description'] = '<strong>'.$product['description'].'</strong>';
      $this->assign("product", $product);
      return $this->render("cart/add.html");
    }
  }
}
