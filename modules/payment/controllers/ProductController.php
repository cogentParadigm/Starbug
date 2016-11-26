<?php
namespace Starbug\Payment;
use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;
class ProductController extends Controller {
	public $routes = array(
		"details" => "details/{path}"
	);
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	function default_action() {
		$this->missing();
	}
	function details($path) {
		$product = $this->models->get("products")->load(["path" => $path]);
		$this->assign("product", $product);
		$this->render("products/details");
	}
}
