<?php
namespace Starbug\Payment;
use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;
class SubscriptionsController extends Controller {
	public $routes = [
		"update" => "update/{id}"
	];
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	function init() {
		$this->assign("model", "orders");
	}
	function update($id) {
		$order = $this->models->get("orders")->load($id);
		$this->assign("order", $order);
		$this->render("subscriptions/update");
	}
}
?>
