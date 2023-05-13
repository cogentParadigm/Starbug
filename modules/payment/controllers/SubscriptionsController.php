<?php
namespace Starbug\Payment;

use Starbug\Db\CollectionFactoryInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Controller;
use Starbug\Core\ModelFactoryInterface;

class SubscriptionsController extends Controller {
  public function __construct(ModelFactoryInterface $models, CollectionFactoryInterface $collections, SessionHandlerInterface $session) {
    $this->models = $models;
    $this->collections = $collections;
    $this->session = $session;
  }
  public function init() {
    $this->assign("model", "orders");
  }
  public function defaultAction() {
    $subscriptions = $this->collections->get(SubscriptionsCollection::class)->query(["owner" => $this->session->getUserId()]);
    $this->assign("subscriptions", $subscriptions);
    $this->render("subscriptions/list.html");
  }
  public function update($id) {
    if ($this->models->get("subscriptions")->success("payment")) {
      $this->response->redirect("subscriptions");
    }
    $subscription = $this->collections->get(SubscriptionsCollection::class)->one(["id" => $id]);
    $this->assign("subscription", $subscription);
    $this->render("subscriptions/update.html");
  }
  public function payment($id) {
    if ($this->models->get("subscriptions")->success("payment")) {
      $this->response->redirect("subscriptions");
    }
    $bill = $this->models->get("bills")->load($id);
    $subscription = $this->collections->get(SubscriptionsCollection::class)->one(["id" => $bill["subscriptions_id"]]);
    $this->assign("subscription", $subscription);
    $this->assign("bill", $bill);
    $this->render("subscriptions/payment.html");
  }
}
