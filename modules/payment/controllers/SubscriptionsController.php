<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\CollectionFactoryInterface;
use Starbug\Core\IdentityInterface;

class SubscriptionsController extends Controller {
  public function __construct(ModelFactoryInterface $models, CollectionFactoryInterface $collections, IdentityInterface $user) {
    $this->models = $models;
    $this->collections = $collections;
    $this->user = $user;
  }
  public function init() {
    $this->assign("model", "orders");
  }
  public function defaultAction() {
    $subscriptions = $this->collections->get("Subscriptions")->query(["owner" => $this->user->userinfo("id")]);
    $this->assign("subscriptions", $subscriptions);
    $this->render("subscriptions/list.html");
  }
  public function update($id) {
    if ($this->models->get("subscriptions")->success("payment")) {
      $this->request->setPost("subscriptions", []);
    }
    $subscription = $this->collections->get("Subscriptions")->one(["id" => $id]);
    $this->assign("subscription", $subscription);
    $this->render("subscriptions/update.html");
  }
  public function payment($id) {
    if ($this->models->get("subscriptions")->success("payment")) {
      $this->request->setPost("subscriptions", []);
    }
    $bill = $this->models->get("bills")->load($id);
    $subscription = $this->collections->get("Subscriptions")->one(["id" => $bill["subscriptions_id"]]);
    $this->assign("subscription", $subscription);
    $this->assign("bill", $bill);
    $this->render("subscriptions/payment.html");
  }
}
