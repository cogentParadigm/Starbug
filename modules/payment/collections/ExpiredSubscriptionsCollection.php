<?php
namespace Starbug\Payment;
use Starbug\Core\Collection;
class ExpiredSubscriptionsCollection extends Collection {
	protected $model = "subscriptions";
	public function build($query, &$ops) {
		$query->condition("subscriptions.active", "1");
		$query->condition("subscriptions.expiration_date", date("Y-m-d H:i:s"), "<=");
		$query->join("payments")->on("payments.subscriptions_id=subscriptions.id");
		$query->select("COUNT(payments.id) as payments");
		$query->group("subscriptions.id");
		return $query;
	}
}
?>
