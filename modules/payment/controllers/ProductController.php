<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;

class ProductController extends Controller {
  public $routes = [
    "details" => "details/{path}"
  ];
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function defaultAction() {
    $this->missing();
  }
  public function details($path) {
    $product = $this->models->get("products")->load(["path" => $path]);
    $this->assign("product", $product);
    $this->render("products/details.html");
  }
}
