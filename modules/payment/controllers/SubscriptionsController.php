<?php
namespace Starbug\Payment;
use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\CollectionFactoryInterface;
use Starbug\Core\IdentityInterface;
class SubscriptionsController extends Controller {
	public $routes = [
		"update" => "update/{id}"
	];
	public function __construct(ModelFactoryInterface $models, CollectionFactoryInterface $collections, IdentityInterface $user) {
		$this->models = $models;
		$this->collections = $collections;
		$this->user = $user;
	}
	function init() {
		$this->assign("model", "orders");
	}
	function default_action() {
		$subscriptions = $this->collections->get("Subscriptions")->query(["owner" => $this->user->userinfo("id")]);
		$this->assign("subscriptions", $subscriptions);
		$this->render("subscriptions/list");
	}
	function update($id) {
		$subscription = $this->collections->get("Subscriptions")->one(["id" => $id]);
		$this->assign("subscription", $subscription);
		$this->render("subscriptions/update");
	}
}
?>
