<?php
namespace Starbug\Payment;
use Starbug\Core\FormCollection;
class BillPaymentFormCollection extends FormCollection {
	public function build($query, &$ops) {
		$query = parent::build($query, $ops);
		if (!empty($ops["bill"])) {
			$query->join("bills")->on("bills.subscriptions_id=subscriptions.id");
			$query->select("bills.id as bill");
			$query->condition("bills.id", $ops["bill"]);
		}
		return $query;
	}
}
?>
