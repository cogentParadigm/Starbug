<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;

class ProductController extends Controller {
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function __invoke($id) {
    $product = $this->models->get("products")->load($id);
    $this->assign("product", $product);
    return $this->render("products/details.html");
  }
}
