<?php
namespace Starbug\Payment;
use Starbug\Core\Collection;
class SubscriptionsCollection extends Collection {
	protected $model = "subscriptions";
	public function build($query, &$ops) {
		if (!empty($ops["owner"])) {
			$query->condition("subscriptions.owner", $ops["owner"]);
		}
		$query->select("subscriptions.product.name as name");
		return $query;
	}
	public function filterRows($rows) {
		foreach ($rows as &$row) {
			$row["bills"] = $this->models->get("bills")->query()
				->condition("subscriptions_id", $row["id"])
				->condition("paid", "0")
				->sort("due_date")
				->all();
		}
		return $rows;
	}
}
