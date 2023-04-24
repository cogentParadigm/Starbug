<?php
namespace Starbug\Payment;

use Starbug\Db\Collection;

class ExpiredSubscriptionsCollection extends Collection {
  protected $model = "bills";
  public function build($query, $ops) {
    if (!empty($ops["id"])) {
      $query->condition("bills.id", $ops["id"]);
    } else {
      $query->condition("bills.scheduled_date", date("Y-m-d H:i:s"), "<=");
      $query->condition("bills.scheduled", "1");
      $query->condition("bills.paid", "0");
    }
    $query->join("subscriptions")->on("subscriptions.id=bills.subscriptions_id");
    $query->join("payments")->on("payments.subscriptions_id=subscriptions.id");
    $query->select("COUNT(payments.id) as payments");
    $query->group("subscriptions.id");
    $query->select("subscriptions.*");
    $query->select("bills.id as bill");
    return $query;
  }
}
